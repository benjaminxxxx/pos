<?php

namespace App\Services\Comercial;

use App\Models\Sucursal;
use Auth;

class SucursalServicio
{
    public static function listarSucursales(int $negocioId)
    {
        $user = Auth::user();

        if (!$user->hasRole('dueno_tienda')) {
            throw new \Exception("Acceso no habilitado para este tipo de usuario.");
        }

        // Verificar que el negocio pertenece al dueño de tienda
        $negocio = $user->negocios()->where('id', $negocioId)->first();
        if (!$negocio) {
            throw new \Exception("No tienes acceso a este negocio.");
        }

        return Sucursal::where('negocio_id', $negocioId)->get();
    }
}