<?php
namespace App\Services\Producto;

use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class InventarioServicio
{
    public static function obtenerAlertasInventario(string $filtro): array
    {
        $query = Stock::with('producto');

        // Filtrado por sucursal si aplica
        if (str_starts_with($filtro, 'sucursal-')) {
            [, $sucursalId] = explode('-', $filtro);
            $query->where('sucursal_id', $sucursalId);
        } elseif (str_starts_with($filtro, needle: 'negocio-')) {
            [, $negocioId] = explode('-', $filtro);
            // Requiere que relaciones productos ↔ sucursales ↔ negocio, adaptar según tu modelo
            $query->whereHas('sucursal', fn($q) => $q->where('negocio_id', $negocioId));
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
                continue; // No es una alerta
            }

            $alertas[] = [
                'product' => $stock->producto->descripcion,
                'stock' => $cantidad,
                'status' => $status,
            ];
        }

        return $alertas;
    }
}
