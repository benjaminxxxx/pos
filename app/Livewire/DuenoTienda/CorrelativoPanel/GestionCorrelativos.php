<?php

namespace App\Livewire\DuenoTienda\CorrelativoPanel;

use App\Models\Correlativo;
use App\Models\Sucursal;
use App\Models\TipoComprobante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class GestionCorrelativos extends Component
{
    public $correlativos;
    public $tiposComprobante;
    public $sucursales;
    public $correlativo;
    public $showForm = false;
    public $isEditing = false;
    
    // Campos del formulario
    public $tipo_comprobante_codigo;
    public $serie;
    public $correlativo_actual = 0;
    public $estado = true;
    public $sucursal_ids = [];
    
    protected $rules = [
        'tipo_comprobante_codigo' => 'required|exists:tipo_comprobantes,codigo',
        'serie' => 'required|string|max:10',
        'correlativo_actual' => 'required|integer|min:0',
        'estado' => 'boolean',
        'sucursal_ids' => 'required|array|min:1',
        'sucursal_ids.*' => 'exists:sucursales,id',
    ];

    public function mount()
    {
        $this->loadCorrelativos();
        $this->loadTiposComprobante();
        $this->loadSucursales();
    }

    public function loadCorrelativos()
    {
        $user = Auth::user();
        $this->correlativos = Correlativo::with(['tipoComprobante', 'sucursales', 'sucursales.negocio'])
            ->whereHas('sucursales', function ($query) use ($user) {
                $query->whereHas('negocio', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->get();
    }

    public function loadTiposComprobante()
    {
        $this->tiposComprobante = TipoComprobante::where('estado', true)->get();
    }

    public function loadSucursales()
    {
        $user = Auth::user();
        $this->sucursales = Sucursal::whereHas('negocio', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('negocio')->get();
    }

    public function render()
    {
        return view('livewire.dueno_tienda.correlativo_panel.gestion-correlativos');
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
    }
 
    public function edit(int $id)
    {
        $correlativo = Correlativo::with('sucursales')->find($id);
        if(!$correlativo){
            LivewireAlert::text('El correlativo ya no existe')
            ->error()
            ->toast()
            ->position('top-end')
            ->show();
            return;
        }
        
        $this->resetForm();
        $this->correlativo = $correlativo;
        $this->tipo_comprobante_codigo = $correlativo->tipo_comprobante_codigo;
        $this->serie = $correlativo->serie;
        $this->correlativo_actual = $correlativo->correlativo_actual;
        $this->estado = $correlativo->estado;
        $this->sucursal_ids = $correlativo->sucursales->pluck('id')->toArray();
        
        $this->showForm = true;
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();
        
        try {
            DB::beginTransaction();
            
            if ($this->isEditing) {
                $correlativo = $this->correlativo;
            } else {
                // Verificar si ya existe un correlativo con la misma serie y tipo de comprobante
                $existente = Correlativo::where('serie', $this->serie)
                    ->where('tipo_comprobante_codigo', $this->tipo_comprobante_codigo)
                    ->first();
                
                if ($existente) {
                    LivewireAlert::text('Ya existe un correlativo con la misma serie y tipo de comprobante')
                        ->error()
                        ->toast()
                        ->position('top-end')
                        ->show();
                    return;
                }
                
                $correlativo = new Correlativo();
            }
            
            $correlativo->tipo_comprobante_codigo = $this->tipo_comprobante_codigo;
            $correlativo->serie = strtoupper($this->serie);
            $correlativo->correlativo_actual = $this->correlativo_actual;
            $correlativo->estado = $this->estado;
            
            $correlativo->save();
            
            // Sincronizar las sucursales
            $correlativo->sucursales()->sync($this->sucursal_ids);
            
            DB::commit();
            
            LivewireAlert::text($this->isEditing ? 'Correlativo actualizado correctamente' : 'Correlativo creado correctamente')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->resetForm();
            $this->loadCorrelativos();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            LivewireAlert::text('Error al guardar el correlativo: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function delete(int $id)
    {
        $correlativo = Correlativo::find($id);
        if(!$correlativo){
            LivewireAlert::text('El correlativo ya no existe')
            ->error()
            ->toast()
            ->position('top-end')
            ->show();
            return;
        }

        try {
            DB::beginTransaction();
            
            // Eliminar las relaciones con sucursales
            $correlativo->sucursales()->detach();
            
            // Eliminar el correlativo
            $correlativo->delete();
            
            DB::commit();
            
            LivewireAlert::text('Correlativo eliminado correctamente')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->loadCorrelativos();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            LivewireAlert::text('Error al eliminar el correlativo: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'correlativo', 'tipo_comprobante_codigo', 'serie', 'correlativo_actual', 
            'estado', 'sucursal_ids', 'showForm', 'isEditing'
        ]);
        
        $this->resetValidation();
    }
}

