<?php
namespace App\Services\Producto;

use App\Models\Stock;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;

class InventarioServicio
{
    public static function obtenerAlertasInventario(string $filtro): array
    {
        $query = Stock::with(['producto', 'sucursal']);

        if ($filtro === 'general') {
            $user = auth()->user();

            if ($user->hasRole('dueno_tienda')) {
                $negocioIds = $user->negocios()->pluck('id');
                $sucursalIds = Sucursal::whereIn('negocio_id', $negocioIds)->pluck('id');

                $query->whereIn('sucursal_id', $sucursalIds);
            } elseif (!$user->hasRole('dueno_sistema')) {
                throw new \Exception('Sin permisos para ver alertas generales.');
            }
        } elseif (str_starts_with($filtro, 'sucursal-')) {
            [, $sucursalId] = explode('-', $filtro);
            $query->where('sucursal_id', $sucursalId);

        } elseif (str_starts_with($filtro, 'negocio-')) {
            [, $negocioId] = explode('-', $filtro);
            // Obtener sucursales del negocio
            $sucursalIds = Sucursal::where('negocio_id', $negocioId)->pluck('id');
            $query->whereIn('sucursal_id', $sucursalIds);

        } else {
            throw new \Exception('Filtro no válido.');
        }

        $stocks = $query->get();

        $alertas = [];

        foreach ($stocks as $stock) {
            $cantidad = $stock->cantidad;
            $minimo = $stock->stock_minimo;

            if ($cantidad <= $minimo) {
                $status = 'critical';
            } elseif ($cantidad <= $minimo * 1.5) {
                $status = 'low';
            } else {
                continue;
            }

            $alertas[] = [
                'product' => $stock->producto->descripcion ?? 'Producto desconocido',
                'stock' => $cantidad,
                'status' => $status,
            ];
        }

        return $alertas;
    }

}
