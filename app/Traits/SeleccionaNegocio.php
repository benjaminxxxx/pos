<?php

namespace App\Traits;

use App\Models\Negocio;
use Illuminate\Support\Facades\Session;

trait SeleccionaNegocio
{
    public $negocios = [];
    public $negocioSeleccionado = null;
    public $mostrarModalSeleccionNegocio = false;
    public $negocioId = null;

    public function mountSeleccionaNegocio()
    {
        $this->negocios = Negocio::where('user_id', auth()->id())->get();
        
        // Verificar si hay un negocio en sesión
        if (Session::has('negocio_seleccionado')) {
            $this->negocioId = Session::get('negocio_seleccionado');
            $this->negocioSeleccionado = Negocio::find($this->negocioId);
        } else {
            // Si no hay negocio en sesión y hay negocios disponibles
            if ($this->negocios->count() > 0) {
                $this->mostrarModalSeleccionNegocio = true;
            }
        }
    }

    public function seleccionarNegocio($negocioId)
    {
        $this->negocioId = $negocioId;
        $this->negocioSeleccionado = Negocio::find($negocioId);
        Session::put('negocio_seleccionado', $negocioId);
        $this->mostrarModalSeleccionNegocio = false;
        $this->dispatch('negocio-seleccionado');
    }

    public function cambiarNegocio()
    {
        $this->mostrarModalSeleccionNegocio = true;
    }

    public function cerrarModalSeleccionNegocio()
    {
        $this->mostrarModalSeleccionNegocio = false;
    }
}

