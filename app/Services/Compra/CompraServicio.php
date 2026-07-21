<?php

namespace App\Services\Compra;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\ProductoEntrada;
use App\Models\Proveedor;
use App\Models\TipoMovimiento;
use App\Services\Caja\MovimientoCajaServicio;
use App\Services\EntradaProductoServicio;
use DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;

class CompraServicio
{
    public function __construct(
        private readonly EntradaProductoServicio $entradaServicio,
        private readonly MovimientoCajaServicio $movimientoCajaServicio,
    ) {
    }
    /**
     * Registra una nueva compra y sus detalles, asegurando la inmutabilidad histórica.
     *
     * @param array $data
     * @return Compra
     * @throws ValidationException
     */
    public function registrarCompra(array $data): Compra
    {
        // 1️⃣ Validación completa de todos los datos
        $validatedData = $this->validarDatos($data);

        return DB::transaction(function () use ($validatedData) {

            // 2️⃣ Obtener proveedor y producto para datos inmutables
            $proveedor = null;
            if ($validatedData['proveedor_id']) {
                $proveedor = Proveedor::find($validatedData['proveedor_id']);
            }

            $detallesProcesados = $this->procesarDetalles($validatedData['detalles']);
            //dd($detallesProcesados,$validatedData['detalles']);
            // 3️⃣ Recálculo y verificación de totales (Mejor Práctica)
            $totalesRecalculados = $this->recalcularTotales($detallesProcesados);

            // 4️⃣ Registrar Compra (Encabezado)
            $compra = Compra::create([
                // Relaciones
                'cuenta_id' => $validatedData['cuenta_id'],
                'sucursal_id' => $validatedData['sucursal_id'],
                'proveedor_id' => $proveedor?->id,

                // Inmutabilidad del Proveedor (Campos con nombre corregido)
                'proveedor_razon_social' => $proveedor?->razon_social,
                'proveedor_nombre_comercial' => $proveedor?->nombre_comercial,
                'proveedor_documento_numero' => $proveedor?->documento_numero, // Nuevo nombre en el migrate

                // Datos del Comprobante
                'tipo_comprobante' => $validatedData['tipo_comprobante'] ?? 'FACTURA',
                'numero_comprobante' => $validatedData['numero_comprobante'] ?? null,
                'forma_pago' => $validatedData['forma_pago'] ?? 'CONTADO',
                'fecha_comprobante' => $validatedData['fecha_comprobante'] ?? now()->toDateString(),
                'fecha_vencimiento' => $validatedData['fecha_vencimiento'] ?? null, // Nuevo campo
                'glosa_o_observacion' => $validatedData['glosa_o_observacion'] ?? null, // Nombre corregido

                // Totales y Control Financiero (Usando los valores recalculados)
                'moneda' => $validatedData['moneda'] ?? 'PEN', // Nuevo campo
                'tipo_cambio' => $validatedData['tipo_cambio'] ?? 1.0000, // Nuevo campo
                'subtotal' => $totalesRecalculados['subtotal_neto'], // Suma de subtotal_neto
                'igv' => $totalesRecalculados['igv_total'], // Suma de monto_igv
                'total' => $totalesRecalculados['total'], // Suma de total_linea
                'monto_pagado' => $validatedData['monto_pagado'] ?? 0, // Nuevo campo
                'estado_pago' => $validatedData['estado_pago'] ?? 'PENDIENTE', // Nuevo campo
                'estado' => $validatedData['estado'] ?? true, // Estado lógico

                // Auditoría
                'created_by' => auth()->check() ? auth()->id() : null,
                'updated_by' => auth()->check() ? auth()->id() : null,
            ]);

            // 5️⃣ Registrar Detalles (Usa el método createMany o saveMany para eficiencia)
            $compra->detalles()->createMany($detallesProcesados);

            // 6️⃣ Disparar evento de Stock (Aquí iría el evento para aumentar el stock)
            // Actualizar Inventario
            $this->registrarEntradaDesdeCompra($compra, $detallesProcesados);

            //SISTEMA DE MOVIMIENTOS DE DINERO
            $tipoCompra = TipoMovimiento::where('slug', 'compra_sistema')
                ->where('activo', true)
                ->first();

            if (!$tipoCompra) {
                throw new Exception('No existe el tipo de movimiento compra_sistema');
            }

            if ($compra->total > 0 && $compra->forma_pago == 'CONTADO') {

                app(MovimientoCajaServicio::class)->registrar([
                    'tipo_movimiento_id' => $tipoCompra->id,
                    'cuenta_id' => $compra->cuenta_id,
                    'sucursal_id' => $compra->sucursal_id,
                    'usuario_id' => auth()->id(), // o usuario sistema si aplica
                    'monto' => $compra->total, // EGRESO
                    'metodo_pago' => $validatedData['forma_pago'] ?? null,
                    'observacion' => "Compra registrada N° {$compra->id}",
                    'fecha' => now(),
                    'referencia_tipo' => Compra::class,
                    'referencia_id' => $compra->id,
                ]);
            }



            return $compra;
        });
    }

    /**
     * Procesa los detalles, añade datos inmutables y calcula montos.
     *
     * @param array $detalles
     * @return array
     */
    protected function procesarDetalles(array $detalles): array
    {
        $detallesProcesados = [];

        foreach ($detalles as $detalle) {
            $producto = Producto::findOrFail($detalle['producto_id']);

            $cantidad = (float) $detalle['cantidad'];
            $costoUnitario = (float) $detalle['costo_unitario'];
            $descPorcentaje = (float) ($detalle['descuento_porcentaje'] ?? 0);
            $igvPorcentaje = (float) ($detalle['porcentaje_igv'] ?? 0);

            // 1. Cálculo de Descuento
            $subtotalBruto = $costoUnitario * $cantidad;
            $descuentoMonto = $subtotalBruto * ($descPorcentaje / 100);

            // 2. Cálculo del Subtotal Neto (antes de impuestos)
            $subtotalNeto = $subtotalBruto - $descuentoMonto;

            // 3. Cálculo del IGV
            $montoIgv = $subtotalNeto * ($igvPorcentaje / 100);

            // 4. Cálculo del Total de la Línea
            $totalLinea = $subtotalNeto + $montoIgv;

            $detallesProcesados[] = [
                // Relaciones
                'producto_id' => $producto->id,

                // Inmutabilidad del Producto
                'producto_nombre' => $producto->descripcion, // Asumiendo que el campo es 'nombre'
                'producto_sku' => $producto->uuid,       // Asumiendo que el campo es 'sku'

                // Cantidades y Unidad
                'cantidad' => $cantidad,
                'unidad_medida' => $detalle['unidad_medida'],
                'factor_conversion' => $detalle['factor_conversion'] ?? 1.0,

                // Precios, Descuentos e Impuestos
                'costo_unitario' => $costoUnitario,
                'descuento_porcentaje' => $descPorcentaje,
                'descuento_monto' => round($descuentoMonto, 4), // Redondeo a 4 decimales
                'subtotal_neto' => round($subtotalNeto, 4),
                'tipo_igv' => $detalle['tipo_igv'] ?? 'GRAVADO',
                'porcentaje_igv' => $igvPorcentaje,
                'monto_igv' => round($montoIgv, 4),

                // Total
                'total_linea' => round($totalLinea, 4),
            ];
        }

        return $detallesProcesados;
    }

    protected function registrarEntradaDesdeCompra(Compra $compra, array $detallesProcesados): void
    {
        foreach ($detallesProcesados as $detalle) {

            $factor = $detalle['factor_conversion'] ?? 1;
            $tipoComprobante = strtoupper($compra->tipo_comprobante ?? 'FACTURA');

            $costoUnitarioBase = (float) $detalle['costo_unitario'];
            $porcentajeIgv = (float) ($detalle['porcentaje_igv'] ?? 0);

            $costoUnitarioFinal = match ($tipoComprobante) {
                'BOLETA' => ($costoUnitarioBase / (1 + ($porcentajeIgv / 100))) / $factor,
                default => $costoUnitarioBase / $factor, // FACTURA, TICKET, etc.
            };

            // ✅ Delega en el servicio: valida, crea la entrada Y aumenta el stock
            $this->entradaServicio->generarEntrada([
                'producto_id' => $detalle['producto_id'],
                'sucursal_id' => $compra->sucursal_id,
                'tipo_entrada' => 'COMPRA',
                'cantidad' => $detalle['cantidad'] * $factor,
                'costo_unitario' => round($costoUnitarioFinal, 4),
                'fecha_ingreso' => $compra->fecha_comprobante,
                'referencia_id' => $compra->id,
                'referencia_tipo' => Compra::class,
            ]);
        }
    }
    /**
     * Suma los totales de los detalles procesados para verificar la cabecera.
     * * @param array $detallesProcesados
     * @return array
     */
    protected function recalcularTotales(array $detallesProcesados): array
    {
        $subtotalNeto = array_sum(Arr::pluck($detallesProcesados, 'subtotal_neto'));
        $igvTotal = array_sum(Arr::pluck($detallesProcesados, 'monto_igv'));
        $total = array_sum(Arr::pluck($detallesProcesados, 'total_linea'));

        // Se utiliza round(..., 2) para el encabezado, ya que generalmente usa 2 decimales para la presentación final.
        return [
            'subtotal_neto' => round($subtotalNeto, 2),
            'igv_total' => round($igvTotal, 2),
            'total' => round($total, 2),
        ];
    }

    /**
     * Valida los datos de entrada para la compra.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    protected function validarDatos(array $data): array
    {
        $rules = [
            'cuenta_id' => 'required|integer|exists:cuentas,id',
            'sucursal_id' => 'required|integer|exists:sucursales,id',
            'proveedor_id' => 'nullable|integer|exists:proveedores,id',

            // Datos del Comprobante
            'tipo_comprobante' => 'required|string|max:50',
            'numero_comprobante' => 'required|string|max:50',
            'forma_pago' => 'required|string|in:CONTADO,CREDITO',
            'fecha_comprobante' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_comprobante', // 🆕 Nuevo
            'glosa_o_observacion' => 'nullable|string', // 🆕 Nuevo

            // Control Financiero
            'moneda' => 'required|string|max:3', // 🆕 Nuevo (ej: PEN)
            'tipo_cambio' => 'required_if:moneda,!=,PEN|nullable|numeric|min:1', // 🆕 Nuevo
            'monto_pagado' => 'nullable|numeric|min:0', // 🆕 Nuevo
            'estado_pago' => 'nullable|string|in:PENDIENTE,PAGADO,PARCIALMENTE PAGADO', // 🆕 Nuevo
            'estado' => 'nullable|boolean',

            // Totales (Se requerirán, pero se recomienda verificarlos contra los detalles)
            'subtotal' => 'required|numeric|min:0',
            'igv' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',

            // Detalles
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|integer|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.0001', // ⬆️ Mayor precisión
            'detalles.*.costo_unitario' => 'required|numeric|min:0',

            // 🆕 Nuevos campos de detalle
            'detalles.*.unidad_medida' => 'required|string|max:50',
            'detalles.*.factor_conversion' => 'nullable|numeric|min:0.000001',
            'detalles.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'detalles.*.tipo_igv' => 'nullable|string|max:50',
            'detalles.*.porcentaje_igv' => 'nullable|numeric|min:0|max:100',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Devolver los datos validados para usar en el servicio
        return $validator->validated();
    }
}
