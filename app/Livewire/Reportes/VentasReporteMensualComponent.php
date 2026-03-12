<?php
namespace App\Livewire\Reportes;

use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class VentasReporteMensualComponent extends Component
{
    #[Reactive]
    public $filters;

    public function render()
    {
        $query = Venta::query();

        // Aplicar filtro de sucursal
        if ($this->filters['sucursal'] !== 'all') {
            $query->where('sucursal_id', $this->filters['sucursal']);
        }

        // Aplicar rango de fechas
        $query->whereBetween('fecha_emision', [$this->filters['fechaInicio'], $this->filters['fechaFin']]);

        // Agrupación por Mes y Año
        $months = $query->select(
            DB::raw("DATE_FORMAT(fecha_emision, '%Y-%m') as mes"),
            DB::raw("COUNT(*) as cantidadTransacciones"),
            DB::raw("SUM(monto_importe_venta) as totalVentas"),
            DB::raw("SUM(monto_igv) as igv"),
            DB::raw("SUM(sub_total) as subtotal")
        )
        ->groupBy('mes')
        ->orderBy('mes', 'asc')
        ->get();

        // Calcular Ticket Promedio y Nombres de Meses
        $months->transform(function ($item) {
            $item->ticketPromedio = $item->cantidadTransacciones > 0 ? $item->totalVentas / $item->cantidadTransacciones : 0;
            $item->nombre_mes = $this->parseMonthName($item->mes);
            return $item;
        });

        // Máximo valor para la barra de tendencia
        $maxValue = $months->max('totalVentas') ?: 1;

        return view('livewire.reportes.ventas-reporte-mensual-component', [
            'months' => $months,
            'maxValue' => $maxValue
        ]);
    }

    private function parseMonthName($monthKey)
    {
        $parts = explode('-', $monthKey);
        $date = \Carbon\Carbon::createFromDate($parts[0], $parts[1], 1);
        return $date->translatedFormat('F Y'); // Ejemplo: Enero 2025
    }
}