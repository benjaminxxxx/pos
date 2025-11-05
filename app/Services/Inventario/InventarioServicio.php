<?php

namespace App\Services\Inventario;

use App\Models\DetalleVenta;
use App\Models\ProductoSalida;
use App\Models\ProductoEntrada;
use App\Models\ProductoSalidaDetalle;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Throwable;

class InventarioServicio
{
    /**
     * Valida y descuenta stock disponible para una salida.
     * - Si ya tiene detalles (ya fue procesada antes), no hace nada.
     * - Si no tiene detalles, busca entradas FIFO disponibles y genera los detalles.
     * - Si no hay stock suficiente, deja pendiente.
     */
    public function validarStock(ProductoSalida $salida)
    {
        // Si ya tiene detalles registrados, asumimos que ya fue procesada.
        if ($salida->detalles()->exists()) {
            return $salida;
        }

        // Fecha límite de búsqueda de entradas (solo si proviene de una venta)
        $fechaLimite = null;

        if ($salida->tipo_salida === 'VENTA' && $salida->referencia_id) {
            $fechaLimite = DetalleVenta::findOrFail($salida->referencia_id)->venta->created_at;
        }
        DB::transaction(function () use ($salida, $fechaLimite) {

            $query = ProductoEntrada::where('producto_id', $salida->producto_id)
                ->where('sucursal_id', $salida->sucursal_id)
                ->where('stock_disponible', '>', 0)
                ->orderBy('fecha_ingreso', 'asc')
                ->lockForUpdate();

            if ($fechaLimite) {
                $query->whereDate('fecha_ingreso', '<=', $fechaLimite);
            }

            $entradas = $query->get();

            $cantidadPorCubrir = $salida->cantidad;

            foreach ($entradas as $entrada) {
                if ($cantidadPorCubrir <= 0)
                    break;

                $cantidadTomada = min($entrada->stock_disponible, $cantidadPorCubrir);

                // Crear detalle de relación entre salida y entrada
                $salida->detalles()->create([
                    'producto_entrada_id' => $entrada->id,
                    'cantidad' => $cantidadTomada,
                    'costo_unitario' => $entrada->costo_unitario,
                    'subtotal' => $cantidadTomada * $entrada->costo_unitario,
                ]);

                // Descontar stock de entrada
                $entrada->decrement('stock_disponible', $cantidadTomada);

                // Actualizar costo de compra en venta_detalle si aplica
                if ($salida->tipo_salida === 'VENTA' && $salida->referencia_id) {
                    $detalleVenta =  DetalleVenta::findOrFail($salida->referencia_id);
                    $detalleVenta->compra_monto = $entrada->costo_unitario;
                    $detalleVenta->save();
                }

                $cantidadPorCubrir -= $cantidadTomada;
            }

            // === 🔄 Actualizar stock general ===
            $stock = Stock::firstOrCreate([
                'producto_id' => $salida->producto_id,
                'sucursal_id' => $salida->sucursal_id,
            ]);

            if ($cantidadPorCubrir <= 0) {
                // Todo el stock fue cubierto
                $salida->update(['estado' => 'procesado']);
                $stock->decrement('cantidad', $salida->cantidad);
            } else {
                // Quedó cantidad pendiente por cubrir
                $salida->update([
                    'estado' => 'pendiente',
                    'cantidad_pendiente' => $cantidadPorCubrir,
                ]);

                $cantidadDescontada = $salida->cantidad - $cantidadPorCubrir;
                if ($cantidadDescontada > 0) {
                    $stock->decrement('cantidad', $cantidadDescontada);
                }
            }

            // Si aún queda cantidad sin cubrir, queda pendiente para reproceso
            if ($cantidadPorCubrir > 0) {
                $salida->update([
                    'pendiente' => true,
                    'cantidad_pendiente' => $cantidadPorCubrir,
                ]);
            }

            // Si no es venta, calcular margen negativo (costo sin ingreso)
            if ($salida->tipo_salida !== 'VENTA') {
                $costoTotal = $salida->detalles()->sum('subtotal');
                $salida->update(['margen' => $costoTotal * -1]);
            }
        });
    }
}
