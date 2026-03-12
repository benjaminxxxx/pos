<?php

// app/Services/Negocio/SucursalServicio.php
namespace App\Services\Negocio;

use App\Models\Sucursal;

class SucursalServicio
{
    public static function porNegocio(int $negocioId)
    {
        return Sucursal::where('negocio_id', $negocioId)
            ->with(['correlativos'])
            ->orderBy('nombre');
    }
}