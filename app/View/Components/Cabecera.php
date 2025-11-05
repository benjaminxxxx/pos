<?php

namespace App\View\Components;

use App\Models\Negocio;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Session;

class Cabecera extends Component
{
    public $negocioActual;
    public function __construct()
    {
        $negocioActualUuid = Session::get('negocio_actual_uuid');
        $this->negocioActual = Negocio::firstWhere('uuid',$negocioActualUuid);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cabecera');
    }
}
