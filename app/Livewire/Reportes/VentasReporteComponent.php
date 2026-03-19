<?php

namespace App\Livewire\Reportes;

use Illuminate\Support\Carbon;
use Livewire\Component;

class VentasReporteComponent extends Component
{
    public $activeTab = 'general';
    
    public $filters = [
        'sucursal' => 'all',
    ];
    protected $listeners = ['filtersUpdated' => 'handleFiltersUpdated'];
    public function mount(){
        $this->filters['fechaInicio'] = session('fecha_inicio',Carbon::now()->format('Y-m-d'));
        $this->filters['fechaFin'] = session('fecha_fin',Carbon::now()->format('Y-m-d'));
    }

    public function handleFiltersUpdated($newFilters)
    {
        $this->filters = $newFilters;
    }
    

    public function render()
    {
        return view('livewire.reportes.ventas-reporte-component');
    }
}