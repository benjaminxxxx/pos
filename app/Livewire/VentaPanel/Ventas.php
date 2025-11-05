<?php

namespace App\Livewire\VentaPanel;

use App\Models\Venta;
use App\Services\Ventas\InformacionVenta;
use App\Services\VentaServicio;
use App\Traits\DatosUtiles\ConSucursales;
use App\Traits\LivewireAlerta;
use App\Traits\SeleccionaNegocio;
use Livewire\Component;
use Livewire\WithPagination;

class Ventas extends Component
{
    use SeleccionaNegocio,WithPagination, LivewireAlerta, ConSucursales;
    public $filtroSucursal;
    public $filtroCliente = '';
    public $filtroDesde;
    public $filtroHasta;
    protected $listeners = ['notaGenerada'];
    public function mount()
    {
        //$this->mountSeleccionaNegocio();
    }
    public function revalidarVenta($uuid)
    {
        try {
            app(VentaServicio::class)->revalidarVenta($uuid);
            $this->alert('success','Venta Revalidada con éxito.');
        } catch (\Throwable $th) {
            $this->alert('error',$th->getMessage());
        }
    }
    public function anularVenta($uuid){
        try {
            app(VentaServicio::class)->anularVenta($uuid);
            $this->alert('success','Venta Anulada Correctamente.');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }
    public function notaGenerada(){
        $this->alert('success', 'Nota de crédito generada correctamente.');
    }
    public function render()
    {
        if (!$this->negocioSeleccionado) {
            return view('seleccionar_negocio');
        }
        $filtros = [
            'sucursal_id' => $this->filtroSucursal,
            'nombre_cliente' => $this->filtroCliente,
            'fecha_desde' => $this->filtroDesde,
            'fecha_hasta' => $this->filtroHasta,
        ];
        $ventas = InformacionVenta::listarVentas($this->negocioSeleccionado->id,$filtros);
        return view('livewire.venta_panel.ventas',
            [
                'ventas' => $ventas,
            ]
        );
    }
}

