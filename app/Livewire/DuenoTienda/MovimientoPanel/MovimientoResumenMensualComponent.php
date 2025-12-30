<?php

namespace App\Livewire\DuenoTienda\MovimientoPanel;

use App\Models\MovimientoCaja;
use App\Models\TipoMovimiento;
use App\Traits\LivewireAlerta;
use Auth;
use Carbon\Carbon;
use Livewire\Component;

class MovimientoResumenMensualComponent extends Component
{
    use LivewireAlerta;

    public $year = '';
    public $month = '';
    public $tablaData = [];
    public $chartData = [];
    public $years;
    public function mount()
    {
        $now = Carbon::now();
        $this->year = $now->year;
        $this->month = $now->month;
        $this->years = range(date('Y') - 5, date('Y'));
        $this->cargarData();
    }

    public function updated($property, $value)
    {
        // Emitir evento cuando cambia año o mes
        if (in_array($property, ['year', 'month'])) {
            $this->cargarData();
        }
    }
    public function cargarData()
    {
        $cuentaId = Auth::user()->cuenta->id;
        $data = $this->obtenerDatos($cuentaId);
        
        $this->tablaData = $data['tabla'];
        $this->chartData = $data['chart'];
        $this->dispatch('regenerarChartMovimiento',$this->chartData);
    }

    private function obtenerDatos($cuentaId)
    {
        $year = $this->year ?: date('Y');
        $month = $this->month ?: date('m');

        if ($this->month) {
            // Por mes: obtener días del mes
            $diasEnMes = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $tabla = [];
            $chartLabels = [];
            $ingresos = [];
            $egresos = [];

            for ($dia = 1; $dia <= $diasEnMes; $dia++) {
                $fecha = Carbon::createFromDate($year, $month, $dia);
                $chartLabels[] = $dia;

                $totalIngresos = MovimientoCaja::where('cuenta_id', $cuentaId)
                    ->whereDate('fecha', $fecha)
                    ->whereHas('tipoMovimiento', fn($q) => $q->where('tipo_flujo', 'ingreso'))
                    ->sum('monto');

                $totalEgresos = MovimientoCaja::where('cuenta_id', $cuentaId)
                    ->whereDate('fecha', $fecha)
                    ->whereHas('tipoMovimiento', fn($q) => $q->where('tipo_flujo', 'egreso'))
                    ->sum('monto');

                $ingresos[] = (float) $totalIngresos;
                $egresos[] = (float) $totalEgresos;

                $tabla[$dia] = [
                    'dia' => $dia,
                    'ingresos' => $totalIngresos,
                    'egresos' => $totalEgresos,
                    'diferencia' => $totalIngresos - $totalEgresos,
                ];
            }
        } else {
            // Por año: obtener meses
            $tabla = [];
            $chartLabels = [];
            $ingresos = [];
            $egresos = [];
            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

            for ($mes = 1; $mes <= 12; $mes++) {
                $chartLabels[] = $meses[$mes - 1];

                $totalIngresos = MovimientoCaja::where('cuenta_id', $cuentaId)
                    ->whereYear('fecha', $year)
                    ->whereMonth('fecha', $mes)
                    ->whereHas('tipoMovimiento', fn($q) => $q->where('tipo_flujo', 'ingreso'))
                    ->sum('monto');

                $totalEgresos = MovimientoCaja::where('cuenta_id', $cuentaId)
                    ->whereYear('fecha', $year)
                    ->whereMonth('fecha', $mes)
                    ->whereHas('tipoMovimiento', fn($q) => $q->where('tipo_flujo', 'egreso'))
                    ->sum('monto');

                $ingresos[] = (float) $totalIngresos;
                $egresos[] = (float) $totalEgresos;

                $tabla[$mes] = [
                    'periodo' => $meses[$mes - 1],
                    'ingresos' => $totalIngresos,
                    'egresos' => $totalEgresos,
                    'diferencia' => $totalIngresos - $totalEgresos,
                ];
            }
        }

        return [
            'tabla' => $tabla,
            'chart' => [
                'labels' => $chartLabels,
                'ingresos' => $ingresos,
                'egresos' => $egresos,
            ]
        ];
    }
    public function render()
    {
        return view('livewire.dueno_tienda.movimiento_panel.movimiento-resumen-mensual-component');
    }
}

