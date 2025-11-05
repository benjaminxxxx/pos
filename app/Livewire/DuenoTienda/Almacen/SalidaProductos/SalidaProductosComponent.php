<?php

namespace App\Livewire\DuenoTienda\Almacen\SalidaProductos;
use App\Models\ProductoSalida;
use App\Traits\ConNegocioSeleccionado;
use Livewire\Component;


class SalidaProductosComponent extends Component
{
    use ConNegocioSeleccionado;
    protected $listeners = ['refrescarSalidaProductos'=>'$refresh'];
    public function mount(){
        $this->cargarNegocioSeleccionado();
    }
    public function render()
    {
        if (!$this->negocioSeleccionado) {
            return view('livewire.dueno_tienda.almacen.salida_productos.salida-productos', [
                'salidas' => collect(), // vacío
            ]);
        }

        // Obtener todas las IDs de las sucursales del negocio
        $sucursalIds = $this->negocioSeleccionado->sucursales()->pluck('id');

        // Buscar todas las salidas de esas sucursales
        $salidas = ProductoSalida::with(['producto', 'sucursal', 'detalles'])
            ->whereIn('sucursal_id', $sucursalIds)
            ->orderByDesc('fecha_salida')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.dueno_tienda.almacen.salida_productos.salida-productos', [
            'salidas' => $salidas,
        ]);
    }
}