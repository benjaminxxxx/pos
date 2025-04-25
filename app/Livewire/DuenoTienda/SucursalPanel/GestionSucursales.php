<?php

namespace App\Livewire\DuenoTienda\SucursalPanel;

use App\Models\Negocio;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class GestionSucursales extends Component
{
    public $sucursales;
    public $negocios;
    public $sucursal;
    public $showForm = false;
    public $isEditing = false;
    
    // Campos del formulario
    public $negocio_id;
    public $nombre;
    public $direccion;
    public $telefono;
    public $email;
    public $es_principal = false;
    public $estado = true;
    
    protected $rules = [
        'negocio_id' => 'required|exists:negocios,id',
        'nombre' => 'required|string|max:255',
        'direccion' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'es_principal' => 'boolean',
        'estado' => 'boolean',
    ];

    public function mount()
    {
        $this->loadSucursales();
        $this->loadNegocios();
    }

    public function loadSucursales()
    {
        $user = Auth::user();
        $this->sucursales = Sucursal::whereHas('negocio', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('negocio')->get();
    }

    public function loadNegocios()
    {
        $this->negocios = Auth::user()->negocios;
    }

    public function render()
    {
        return view('livewire.dueno_tienda.sucursal_panel.gestion-sucursales');
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
        
        // Si solo hay un negocio, seleccionarlo automáticamente
        if ($this->negocios->count() === 1) {
            $this->negocio_id = $this->negocios->first()->id;
        }
    }

    public function edit(String $uuid)
    {
        $sucursal = Sucursal::where('uuid', $uuid)->first();
        if(!$sucursal){
            LivewireAlert::text('La sucursal ya no existe')
            ->error()
            ->toast()
            ->position('top-end')
            ->show();
            return;
        }
        
        $this->resetForm();
        $this->sucursal = $sucursal;
        $this->negocio_id = $sucursal->negocio_id;
        $this->nombre = $sucursal->nombre;
        $this->direccion = $sucursal->direccion;
        $this->telefono = $sucursal->telefono;
        $this->email = $sucursal->email;
        $this->es_principal = $sucursal->es_principal;
        $this->estado = $sucursal->estado;
        
        $this->showForm = true;
        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();
        
        try {
            if ($this->isEditing) {
                $sucursal = $this->sucursal;
            } else {
                $sucursal = new Sucursal();
                $sucursal->uuid = Str::uuid();
            }
            
            $sucursal->negocio_id = $this->negocio_id;
            $sucursal->nombre = $this->nombre;
            $sucursal->direccion = $this->direccion;
            $sucursal->telefono = $this->telefono;
            $sucursal->email = $this->email;
            $sucursal->es_principal = $this->es_principal;
            $sucursal->estado = $this->estado;
            
            $sucursal->save();
            
            // Si es la primera sucursal del negocio, marcarla como principal
            $countSucursales = Sucursal::where('negocio_id', $this->negocio_id)->count();
            if ($countSucursales === 1) {
                $sucursal->es_principal = true;
                $sucursal->save();
            }
            
            LivewireAlert::text($this->isEditing ? 'Sucursal actualizada correctamente' : 'Sucursal creada correctamente')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->resetForm();
            $this->loadSucursales();
            
        } catch (\Exception $e) {
            LivewireAlert::text('Error al guardar la sucursal: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function delete(String $uuid)
    {
        $sucursal = Sucursal::where('uuid', $uuid)->first();
        if(!$sucursal){
            LivewireAlert::text('La sucursal ya no existe')
            ->error()
            ->toast()
            ->position('top-end')
            ->show();
            return;
        }

        try {
            // Verificar si es la única sucursal del negocio
            $countSucursales = Sucursal::where('negocio_id', $sucursal->negocio_id)->count();
            if ($countSucursales <= 1) {
                LivewireAlert::text('No se puede eliminar la única sucursal del negocio')
                    ->error()
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }
            
            // Verificar si es la sucursal principal
            if ($sucursal->es_principal) {
                LivewireAlert::text('No se puede eliminar la sucursal principal')
                    ->error()
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }
            
            $sucursal->delete();
            
            LivewireAlert::text('Sucursal eliminada correctamente')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->loadSucursales();
            
        } catch (\Exception $e) {
            LivewireAlert::text('Error al eliminar la sucursal: ' . $e->getMessage())
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
            'sucursal', 'negocio_id', 'nombre', 'direccion', 'telefono', 
            'email', 'es_principal', 'estado', 'showForm', 'isEditing'
        ]);
        
        $this->resetValidation();
    }
}

