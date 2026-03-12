<?php

namespace App\Livewire\Reportes;

use App\Traits\DatosUtiles\ConSucursales;
use Livewire\Component;
use Session;

class VentasReporteFiltersComponent extends Component
{
    use ConSucursales;
    public $filters;
    public function mount(){
        if($this->sucursales->count()==1){
            $this->filters['sucursal'] = $this->sucursales[0]->id;
        }
    }

    public function updatedFilters($filters)
    {
        Session::put('fecha_inicio',$this->filters['fechaInicio']);
        Session::put('fecha_fin',$this->filters['fechaFin']);
        $this->dispatch('filtersUpdated', $this->filters);
    }

    public function render()
    {
        return view('livewire.reportes.ventas-reporte-filters-component');
    }
}