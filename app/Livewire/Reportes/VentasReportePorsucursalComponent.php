<?php
namespace App\Livewire\Reportes;

use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class VentasReportePorsucursalComponent extends Component
{
    #[Reactive]
    public $filters;

    public function render()
    {
        $query = Venta::query()
            ->with('sucursal'); // Cargamos la relación para el nombre

        // Filtro opcional por sucursal específica (si el usuario no eligió "all")
        if ($this->filters['sucursal'] !== 'all') {
            $query->where('sucursal_id', $this->filters['sucursal']);
        }

        $query->whereBetween('fecha_emision', [
            $this->filters['fechaInicio'], 
            $this->filters['fechaFin']
        ]);

        // Agregación por sucursal
        $branches = $query->select(
            'sucursal_id',
            DB::raw("COUNT(*) as cantidadTransacciones"),
            DB::raw("SUM(monto_importe_venta) as totalVentas"),
            DB::raw("SUM(monto_igv) as igv")
        )
        ->groupBy('sucursal_id')
        ->get()
        ->map(function ($item) {
            $item->nombre = $item->sucursal->nombre ?? 'Sin Sucursal';
            $item->ticketPromedio = $item->cantidadTransacciones > 0 ? $item->totalVentas / $item->cantidadTransacciones : 0;
            return $item;
        })
        ->sortByDesc('totalVentas');

        $totalGeneral = $branches->sum('totalVentas');

        return view('livewire.reportes.ventas-reporte-porsucursal-component', [
            'branches' => $branches,
            'totalGeneral' => $totalGeneral ?: 1 // Evitar división por cero
        ]);
    }
}