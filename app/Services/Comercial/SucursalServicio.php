<?php

namespace App\Services\Comercial;

use App\Models\Sucursal;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;

class SucursalServicio
{
    public static function listarSucursales(int $negocioId)
    {
        $user = Auth::user();

        if (!$user->hasRole('dueno_tienda')) {
            throw new AuthorizationException(
                'No tienes permisos para acceder a las sucursales.'
            );
        }

        $negocioActivo = $user->negocioActivo;

        if (!$negocioActivo || $negocioActivo->id !== $negocioId) {
            throw new AuthorizationException(
                'No tienes acceso a este negocio.'
            );
        }

        return Sucursal::where('negocio_id', $negocioId)
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();
    }
}