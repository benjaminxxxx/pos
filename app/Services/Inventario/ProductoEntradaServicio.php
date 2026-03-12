<?php

// app/Services/Inventario/ProductoEntradaServicio.php
namespace App\Services\Inventario;

use App\Models\ProductoEntrada;

class ProductoEntradaServicio
{
    // Entradas de una sucursal específica
    public static function porSucursal(int $sucursalId)
    {
        return ProductoEntrada::where('sucursal_id', $sucursalId)
            ->with(['producto', 'sucursal'])
            ->orderByDesc('fecha_ingreso');
    }

    // Entradas de todas las sucursales de un negocio
    public static function porNegocio(int $negocioId)
    {
        return ProductoEntrada::whereHas('sucursal', function ($q) use ($negocioId) {
                $q->where('negocio_id', $negocioId);
            })
            ->with(['producto', 'sucursal'])
            ->orderByDesc('fecha_ingreso');
    }

    // Entradas de todos los negocios de una cuenta
    public static function porCuenta(int $cuentaId)
    {
        return ProductoEntrada::whereHas('sucursal.negocio', function ($q) use ($cuentaId) {
                $q->where('cuenta_id', $cuentaId);
            })
            ->with(['producto', 'sucursal.negocio'])
            ->orderByDesc('fecha_ingreso');
    }
}