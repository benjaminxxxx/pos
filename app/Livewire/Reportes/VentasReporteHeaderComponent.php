<?php

namespace App\Livewire\Reportes;

use App\Traits\LivewireAlerta;
use Livewire\Component;

class VentasReporteHeaderComponent extends Component
{
    use LivewireAlerta;
    public function exportExcel(){
        $this->alert('success','Función aún no disponible');
    }
    public function exportPdf(){
        $this->alert('success','Función aún no disponible');
    }
    public function render()
    {
        return view('livewire.reportes.ventas-reporte-header-component');
    }
}