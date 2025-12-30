<?php

namespace App\Livewire\DuenoTienda\MovimientoPanel;

use App\Models\MovimientoCaja;
use App\Models\TipoMovimiento;
use App\Services\Caja\MovimientoCajaServicio;
use App\Traits\LivewireAlerta;
use Auth;
use Livewire\Component;

class MovimientosComponent extends Component
{
    use LivewireAlerta;
    public $filtroFlujo = '';
    public $filtroTipo = '';
    public $filtroFecha = '';
    public $tiposMovimiento = [];
    public function mount()
    {
        $this->tiposMovimiento = TipoMovimiento::where('activo', true)->get();
    }
    public function limpiarFiltros()
    {
        $this->reset(['filtroFlujo', 'filtroTipo', 'filtroFecha']);
    }

    public function render()
    {
        // Preparamos el array de filtros
        $filtros = [
            'flujo' => $this->filtroFlujo,
            'tipo_id' => $this->filtroTipo,
            'fecha' => $this->filtroFecha,
        ];
        
        // Llamamos al servicio
        $movimientos = app(MovimientoCajaServicio::class)
            ->listar($filtros, auth()->user()->cuenta->id)
            ->paginate(20);

        return view('livewire.dueno_tienda.movimiento_panel.movimientos-component', [
            'movimientos' => $movimientos
        ]);
    }

}

