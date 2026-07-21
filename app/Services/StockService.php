<?php

namespace App\Services;

use App\Exceptions\StockInsuficienteException;
use App\Models\Stock;

/**
 * Único punto de escritura sobre la tabla `stocks`.
 * Solo debe ser invocado desde EntradaProductoServicio y SalidaProductoServicio.
 * Requiere ejecutarse dentro de una transacción abierta por el llamador
 * (el lockForUpdate solo tiene efecto real dentro de una transacción).
 */
class StockService
{
    public function incrementar(int $productoId, int $sucursalId, float $cantidad): Stock
    {
        $stock = Stock::lockForUpdate()->firstOrCreate(
            ['producto_id' => $productoId, 'sucursal_id' => $sucursalId],
            ['cantidad' => 0, 'stock_minimo' => 0]
        );

        $stock->increment('cantidad', $cantidad);

        return $stock->fresh();
    }

    /**
     * @throws StockInsuficienteException
     */
    public function decrementar(int $productoId, int $sucursalId, float $cantidad): Stock
    {
        $stock = Stock::where('producto_id', $productoId)
            ->where('sucursal_id', $sucursalId)
            ->lockForUpdate()
            ->first();

        $disponible = $stock ? (float) $stock->cantidad : 0.0;

        if (!$stock || $disponible < $cantidad) {
            throw new StockInsuficienteException($productoId, $sucursalId, $disponible, $cantidad);
        }

        $stock->decrement('cantidad', $cantidad);

        return $stock->fresh();
    }
}