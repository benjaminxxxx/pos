<?php

namespace App\Livewire\DuenoTienda\CorrelativoPanel;

use App\Models\Correlativo;
use App\Models\Sucursal;
use App\Models\TipoComprobante;
use App\Services\Comercial\SucursalServicio;
use App\Services\Facturacion\Configuracion\CorrelativoServicio;
use App\Traits\LivewireAlerta;
use App\Traits\SeleccionaNegocio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
//gestion correlativo
class GestionCorrelativos extends Component
{
    use SeleccionaNegocio, LivewireAlerta;
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
    protected $listeners = ['negocio-seleccionado'=> 'regenerarValores'];

    protected $rules = [
        'tipo_comprobante_codigo' => 'required|exists:tipo_comprobantes,codigo',
        'serie' => 'required|string|max:10',
        'correlativo_actual' => 'required|integer|min:0',
        'estado' => 'boolean',
        'sucursal_ids' => 'required|array|min:1',
        'sucursal_ids.*' => 'exists:sucursales,id',
    ];
    #region Base
    public function mount()
    {
        $this->mountSeleccionaNegocio();
        $this->loadCorrelativos();
        $this->loadTiposComprobante();
        $this->loadSucursales();
    }
    public function render()
    {
        return view('livewire.dueno_tienda.correlativo_panel.gestion-correlativos');
    }
    #endregion
    #region Metodos
    public function regenerarValores(){
        $this->loadCorrelativos();
        $this->loadSucursales();
        $this->resetForm();
    }
    public function loadCorrelativos()
    {
        try {
            $this->correlativos = CorrelativoServicio::listarPorNegocio($this->negocioSeleccionado->id);
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
            $this->correlativos = collect();
        }
    }

    public function loadTiposComprobante()
    {
        $this->tiposComprobante = TipoComprobante::where('estado', true)->get();
    }

    public function loadSucursales()
    {
        try {
            $this->sucursales = SucursalServicio::listarSucursales($this->negocioSeleccionado->id);
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
            $this->sucursales = collect();
        }
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
        if (!$correlativo) {
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
            $data = [
                'tipo_comprobante_codigo' => $this->tipo_comprobante_codigo,
                'serie' => $this->serie,
                'correlativo_actual' => $this->correlativo_actual,
                'estado' => $this->estado,
                'sucursales' => $this->sucursal_ids,
                'negocio_id' => $this->negocioSeleccionado->id,
            ];

            CorrelativoServicio::guardar($data, $this->isEditing ? $this->correlativo : null);

            $this->alert('success', $this->isEditing ? 'Correlativo actualizado correctamente' : 'Correlativo creado correctamente');

            $this->resetForm();
            $this->loadCorrelativos();

        } catch (\Exception $e) {
            $this->alert('error', 'Error al guardar el correlativo: ' . $e->getMessage());
        }
    }

    public function delete(int $id)
    {
        try {
            CorrelativoServicio::eliminar($id);

            $this->alert('success', 'Correlativo eliminado correctamente');
            $this->loadCorrelativos();

        } catch (\Exception $e) {
            $this->alert('error', 'Error al eliminar el correlativo: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'correlativo',
            'tipo_comprobante_codigo',
            'serie',
            'correlativo_actual',
            'estado',
            'sucursal_ids',
            'showForm',
            'isEditing'
        ]);

        $this->resetValidation();
    }
    #endregion
}

