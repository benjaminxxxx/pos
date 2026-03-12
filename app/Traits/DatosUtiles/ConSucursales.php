<?php

namespace App\Traits\DatosUtiles;
use Illuminate\Support\Facades\Auth;

trait ConSucursales
{
    public $sucursales;
    public function bootConSucursales()
    {
        $this->cargarSucursales();
    }
    public function cargarSucursales()
    {
         $user = Auth::user();

        if (!$user || !$user->negocio_activo) {
            $this->sucursales = collect();
            return;
        }

        $this->sucursales = $user->negocio_activo
            ->sucursales()
            ->orderBy('nombre')
            ->get();
    }

}