<?php

namespace App\Livewire\DuenoTienda\Almacen\EntradaProductos;
use App\Models\ProductoEntrada;
use Livewire\Component;


class EntradaProductosComponent extends Component
{
    protected $listeners = ['refrescarEntradaProductos'=>'$refresh'];
    public function render()
    {
        $entradas = ProductoEntrada::paginate(20);
        return view('livewire.dueno_tienda.almacen.entrada_productos.entrada-productos',[
            'entradas'=>$entradas
        ]);
    }
}