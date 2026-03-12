<?php
//Entrada de productos
namespace App\Livewire\DuenoTienda\Almacen\EntradaProductos;
use App\Services\Inventario\ProductoEntradaServicio;
use Auth;
use Livewire\Component;


class EntradaProductosComponent extends Component
{
    protected $listeners = ['refrescarEntradaProductos'=>'$refresh'];
    public function render()
    {
        $negocio = Auth::user()->negocio_activo;
        $entradas = ProductoEntradaServicio::porNegocio($negocio->id)->paginate();
        return view('livewire.dueno_tienda.almacen.entrada_productos.entrada-productos',[
            'entradas'=>$entradas
        ]);
    }
}