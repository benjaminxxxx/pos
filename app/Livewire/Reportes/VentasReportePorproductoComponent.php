<?php
namespace App\Livewire\Reportes;

use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class VentasReportePorproductoComponent extends Component
{
    #[Reactive]
    public $filters;

    public function render()
    {
        // Iniciamos la consulta en detalles
        $query = DetalleVenta::query()
            ->whereHas('venta', function ($q) {
                // Filtro de sucursal
                if ($this->filters['sucursal'] !== 'all') {
                    $q->where('sucursal_id', $this->filters['sucursal']);
                }
                // Filtro de fechas
                $q->whereBetween('fecha_emision', [
                    $this->filters['fechaInicio'], 
                    $this->filters['fechaFin']
                ]);
            });

        // Agrupamos por producto
        $products = $query->select(
            'producto_id',
            'descripcion as nombre',
            DB::raw("SUM(cantidad) as cantidadVendida"),
            DB::raw("SUM(monto_valor_venta) as ingresoTotal"),
            DB::raw("SUM(compra_monto) as costoTotal"),
            DB::raw("SUM(utilidad_neta) as utilidadTotal")
        )
        ->groupBy('producto_id', 'descripcion')
        ->orderBy('ingresoTotal', 'desc')
        ->get();

        $totalIngresosGeneral = $products->sum('ingresoTotal') ?: 1;

        return view('livewire.reportes.ventas-reporte-porproducto-component', [
            'products' => $products,
            'totalIngresos' => $totalIngresosGeneral
        ]);
    }
}