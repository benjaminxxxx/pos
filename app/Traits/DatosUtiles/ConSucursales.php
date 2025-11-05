<?php

namespace App\Traits\DatosUtiles;
use App\Models\Negocio;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Session;

trait ConSucursales
{
    public $sucursales;
    public function bootConSucursales()
    {
        $this->cargarSucursales();
    }
    public function cargarSucursales()
    {
        $user = auth()->user();
        $negocioUuid = Session::get('negocio_actual_uuid');

        if (!$user || !$user->cuenta) {
            $this->sucursales = collect();
            return;
        }

        $negocio = Negocio::query()
            ->where('cuenta_id', $user->cuenta->id)
            ->when($negocioUuid, fn($q) => $q->where('uuid', $negocioUuid))
            ->first();

        if (!$negocio) {
            // No se encontró el negocio activo
            $this->sucursales = collect();
            return;
        }

        $this->sucursales = Sucursal::where('negocio_id', $negocio->id)
            ->orderBy('nombre')
            ->get();
    }

}