<?php

namespace App\Livewire\Reportes;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Reactive;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class VentasReporteGeneralComponent extends Component
{
    use WithPagination, WithoutUrlPagination;
    #[Reactive]
    public $filters;

    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Query base (una sola fuente de verdad)
        |--------------------------------------------------------------------------
        */
        $baseQuery = Venta::query()
            ->with(['sucursal', 'cliente']);

        // Filtro por sucursal
        if ($this->filters['sucursal'] !== 'all') {
            $baseQuery->where('sucursal_id', $this->filters['sucursal']);
        }

        // Filtro por rango de fechas
        $baseQuery->whereBetween('fecha_emision', [
            $this->filters['fechaInicio'] . ' 00:00:00',
            $this->filters['fechaFin'] . ' 23:59:59',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. KPIs (dataset COMPLETO, sin paginación)
        |--------------------------------------------------------------------------
        */
        $stats = (clone $baseQuery)->selectRaw('
        SUM(monto_importe_venta) AS total,
        SUM(monto_igv) AS igv,
        SUM(sub_total) AS subtotal,
        COUNT(*) AS count
    ')->first();

        $averageTicket = $stats->count > 0
            ? $stats->total / $stats->count
            : 0;

        $kpis = [
            [
                'label' => 'Total Vendido',
                'value' => 'S/. ' . number_format($stats->total ?? 0, 2),
                'icon' => 'fa-solid fa-money-bill-wave',
                'color' => 'bg-green-500',
            ],
            [
                'label' => 'Cantidad de Ventas',
                'value' => $stats->count ?? 0,
                'icon' => 'fa-solid fa-cart-shopping',
                'color' => 'bg-blue-500',
            ],
            [
                'label' => 'Ticket Promedio',
                'value' => 'S/. ' . number_format($averageTicket, 2),
                'icon' => 'fa-solid fa-chart-line',
                'color' => 'bg-purple-500',
            ],
            [
                'label' => 'IGV Cobrado',
                'value' => 'S/. ' . number_format($stats->igv ?? 0, 2),
                'icon' => 'fa-solid fa-percent',
                'color' => 'bg-orange-500',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | 3. Tabla paginada (solo lo que se muestra)
        |--------------------------------------------------------------------------
        */
        $sales = (clone $baseQuery)
            ->orderBy('fecha_emision', 'desc')
            ->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | 4. Render
        |--------------------------------------------------------------------------
        */
        $totalSales = $stats->total ?? 0;
        $totalIGV = $stats['igv'] ?? 0;

        return view('livewire.reportes.ventas-reporte-general-component', [
            'sales' => $sales,
            'kpis' => $kpis,
            'stats' => [
                'total' => $stats->total ?? 0,
                'igv' => $stats->igv ?? 0,
                'subtotal' => $stats->subtotal ?? 0,
                'count' => $stats->count ?? 0,
                'average' => $averageTicket,
            ],
            'totalIGV' => $totalIGV,
            'totalSales' => $totalSales,
        ]);
    }

}