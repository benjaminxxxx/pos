<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\ProductoEntrada;
use App\Models\ProductoSalida;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class SalidaProductoServicio
{
    public function __construct(private readonly StockService $stockService)
    {
    }
    /**
     * Punto de entrada único: crea la salida si no existe (por referencia),
     * y en cualquier caso intenta cubrir la cantidad_pendiente restante
     * contra entradas FIFO disponibles. Llamarlo de nuevo sobre una salida
     * ya existente (ej. revalidación posterior a nueva compra) es seguro:
     * solo avanza lo que falte, nunca duplica lo ya cubierto.
     *
     * @throws ValidationException
     */
    public function generarSalida(array $data): ProductoSalida
    {
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        $tieneReferencia = !empty($validated['referencia_id']) && !empty($validated['referencia_tipo']);
        

        return DB::transaction(function () use ($validated, $tieneReferencia) {

            $salida = null;

            if ($tieneReferencia) {
                $salida = ProductoSalida::where('referencia_id', $validated['referencia_id'])
                    ->where('referencia_tipo', $validated['referencia_tipo'])
                    ->where('producto_id', $validated['producto_id'])
                    ->where('sucursal_id', $validated['sucursal_id'])
                    ->lockForUpdate()
                    ->first();
            }

            if (!$salida) {
                $validated['created_by'] = auth()->id();
                $validated['estado'] = 'pendiente';
                $validated['cantidad_pendiente'] = $validated['cantidad'];
                unset($validated['costo_unitario']);

                $salida = ProductoSalida::create($validated);
            }
            if ((float) $salida->cantidad_pendiente > 0) {
                
                $this->consumirPendiente($salida);
            }

            return $salida->fresh();
        });
    }

    /**
     * Intenta cubrir salida->cantidad_pendiente contra entradas FIFO
     * disponibles en ese momento. Solo descuenta stock por lo que
     * efectivamente se cubre en ESTA ejecución (nunca reprocesa lo
     * que ya fue cubierto en una llamada anterior).
     */
    protected function consumirPendiente(ProductoSalida $salida): void
    {
        $cantidadPorCubrir = (float) $salida->cantidad_pendiente;

        $fechaLimite = null;
        if ($salida->tipo_salida === 'VENTA' && $salida->referencia_id) {
            $fechaLimite = optional(DetalleVenta::find($salida->referencia_id))->created_at;
        }

        $query = ProductoEntrada::where('producto_id', $salida->producto_id)
            ->where('sucursal_id', $salida->sucursal_id)
            ->where('stock_disponible', '>', 0)
            ->orderBy('fecha_ingreso')
            ->lockForUpdate();

        if ($fechaLimite) {
            $query->whereDate('fecha_ingreso', '<=', $fechaLimite);
        }

        foreach ($query->get() as $entrada) {
            if ($cantidadPorCubrir <= 0) {
                break;
            }

            $cantidadTomada = min((float) $entrada->stock_disponible, $cantidadPorCubrir);

            $salida->detalles()->create([
                'producto_entrada_id' => $entrada->id,
                'cantidad' => $cantidadTomada,
                'costo_unitario' => $entrada->costo_unitario,
                'subtotal' => $cantidadTomada * $entrada->costo_unitario,
            ]);

            $entrada->decrement('stock_disponible', $cantidadTomada);

            if ($salida->tipo_salida === 'VENTA' && $salida->referencia_id) {
                DetalleVenta::whereKey($salida->referencia_id)
                    ->update(['compra_monto' => $entrada->costo_unitario]);
            }

            $cantidadPorCubrir -= $cantidadTomada;
        }

        $cantidadCubiertaAhora = (float) $salida->cantidad_pendiente - $cantidadPorCubrir;

        // Único descuento real de stock, solo por lo cubierto en esta pasada
        if ($cantidadCubiertaAhora > 0) {
            $this->stockService->decrementar($salida->producto_id, $salida->sucursal_id, $cantidadCubiertaAhora);
        }

        $salida->update([
            'estado' => $cantidadPorCubrir <= 0 ? 'procesado' : 'pendiente',
            'cantidad_pendiente' => $cantidadPorCubrir,
        ]);

        if ($salida->tipo_salida !== 'VENTA') {
            $costoTotal = $salida->detalles()->sum('subtotal');
            $salida->update(['margen' => $costoTotal * -1]);
        }
    }

    protected function rules(): array
    {
        return [
            'fecha_salida' => 'required|date',
            'producto_id' => 'required|integer|exists:productos,id',
            'sucursal_id' => 'required|integer|exists:sucursales,id',
            'tipo_salida' => 'required|string|max:30',
            'cantidad' => 'required|numeric|min:0.001',
            'costo_unitario' => 'nullable|numeric|min:0',
            'referencia_id' => 'nullable|integer|required_with:referencia_tipo',
            'referencia_tipo' => 'nullable|string|max:50|required_with:referencia_id',
        ];
    }
}