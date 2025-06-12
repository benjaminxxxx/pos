<?php

namespace App\Livewire\VentaPanel;

use App\Services\Ventas\InformacionVenta;
use App\Traits\LivewireAlerta;
use App\Traits\SeleccionaNegocio;
use Livewire\Component;
use Livewire\WithPagination;

class Ventas extends Component
{
    use SeleccionaNegocio,WithPagination, LivewireAlerta;
    protected $listeners = ['notaGenerada'];
    public function mount()
    {
        $this->mountSeleccionaNegocio();
    }
    public function eliminarVenta($uuid){
        try {
            InformacionVenta::eliminarVenta($uuid);
            $this->alert('success','Venta eliminada correctamente.');
        } catch (\Exception $e) {
            $this->alert('error', 'Error al eliminar la venta: ' . $e->getMessage());
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

        $ventas = InformacionVenta::listarVentas($this->negocioSeleccionado->id);
        return view('livewire.venta_panel.ventas',
            [
                'ventas' => $ventas,
            ]
        );
    }

}

