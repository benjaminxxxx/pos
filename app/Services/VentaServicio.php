<?php
// app/Services/VentaServicio.php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\Negocio;
use App\Models\ProductoEntrada;
use App\Models\ProductoSalida;
use App\Models\Stock;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaMetodoPago;
use App\Services\Inventario\InventarioServicio;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class VentaServicio
{
    public function anularVenta($uuid)
    {
        DB::transaction(function () use ($uuid) {
            $venta = Venta::with(['detalles'])->where('uuid', $uuid)->first();

            if (!$venta) {
                throw new Exception('Venta no encontrada.');
            }

            if ($venta->estado === 'anulado') {
                throw new Exception('La venta ya está anulada.');
            }

            foreach ($venta->detalles as $detalle) {
                // Buscar la salida vinculada a este detalle
                $salida = ProductoSalida::with('detalles')
                    ->where('referencia_id', $detalle->id)
                    ->where('referencia_tipo', get_class($detalle))
                    ->first();

                // Si no hay salida → nada que revertir
                if (!$salida) {
                    continue;
                }

                // === CASO 1: Salida sin detalles (no se afectó stock) ===
                if ($salida->detalles->isEmpty()) {
                    $salida->delete();
                    continue;
                }

                // === CASO 2: Salida con detalles (sí afectó stock) ===
                foreach ($salida->detalles as $detalleSalida) {
                    $entrada = ProductoEntrada::create([
                        'producto_id' => $salida->producto_id,
                        'sucursal_id' => $salida->sucursal_id,
                        'cantidad' => $detalleSalida->cantidad,
                        'stock_disponible' => $detalleSalida->cantidad,
                        'costo_unitario' => $detalleSalida->costo_unitario,
                        'tipo_entrada' => 'ANULACION VENTA',
                        'referencia_id' => $venta->id,
                        'referencia_tipo' => get_class($venta),
                        'fecha_ingreso' => now(),
                        'created_by' => auth()->id(),
                    ]);

                    // Actualizar stock global
                    $stock = Stock::firstOrCreate([
                        'producto_id' => $salida->producto_id,
                        'sucursal_id' => $salida->sucursal_id,
                    ]);
                    $stock->increment('cantidad', $detalleSalida->cantidad);
                }

                // Marcar salida como anulada
                $salida->update(['estado' => 'anulado']);
            }

            // Actualizar estado general de la venta
            $venta->update([
                'estado' => 'anulado',
                'fecha_anulacion' => now(),
            ]);
        });
    }
    public function revalidarVenta($uuid)
    {
        $venta = Venta::firstWhere('uuid', $uuid);
        if (!$venta) {
            throw new Exception("La venta no existe.");
        }
        //Revisamos si cada detalle de esta venta tiene costo
        foreach ($venta->detalles as $detalle) {

            //verificar si tiene salida registrada

            $cantidad = $detalle->cantidad ?? 1;
            $factor = $detalle->factor ?? 1;
            $totalUnidades = $cantidad * $factor;

            $data = [
                'producto_id' => $detalle->producto_id,
                'sucursal_id' => $detalle->venta->sucursal_id,
                'tipo_salida' => 'VENTA',
                'cantidad' => $totalUnidades,
                'costo_unitario' => 0,
                'fecha_salida' => $detalle->venta->fecha_emision,
                'referencia_id' => $detalle->id,
                'referencia_tipo' => get_class($detalle),
                'created_by' => auth()->id(),
                'estado' => 'pendiente',
            ];

            $salida = app(SalidaProductoServicio::class)->generarSalida($data);

            app(InventarioServicio::class)->validarStock($salida);

        }

    }
    public static function generarVenta($data)
    {
        $venta = $data['venta'];
        $tipoComprobante = $data['tipo_comprobante_codigo'] ?? null;
        $cliente = $data['cliente'] ?? null;
        $fechaEmision = $data['fecha_emision'] ?? null;


        if (!$tipoComprobante || !in_array($tipoComprobante, ['01', '03', 'ticket'])) {
            throw new Exception('Debe seleccionar un tipo de comprobante.');
        }
        /*
        if ($tipoComprobante == 'ticket') {
            $tipoComprobante = null; // Para tickets no se requiere tipo de comprobante específico, se va a facturar o boletear despues
        }*/
        if ($tipoComprobante == '01' && !$cliente) {
            throw new Exception('Debe agregar un cliente para emitir una factura.');
        }
        if (!isset($venta)) {
            throw new Exception('Falta agregar la venta.');
        }
        if (!isset($venta['productos']) || count($venta['productos']) == 0) {
            throw new Exception('Falta agregar productos a la venta.');
        }

        $pagado = (float) $data['totalPago'];
        $monto_importe_venta = (float) $venta['monto_importe_venta'];

        if ($pagado != $monto_importe_venta) {
            $diferencia = abs($pagado - $monto_importe_venta);

            if ($diferencia > 0.05) {
                throw new Exception(
                    "El monto ingresado por el cliente (S/ " . number_format($pagado, 2) .
                    ") no coincide con el total de la venta (S/ " . number_format($monto_importe_venta, 2) . "). " .
                    "Esto podría deberse a un error de ingreso o un redondeo inválido. " .
                    "Por favor, ingrese el monto exacto: S/ " . number_format($monto_importe_venta, 2)
                );
            }

            // Validar si el monto ingresado termina en una fracción válida
            $decimal = fmod($pagado, 1); // obtiene solo la parte decimal
            $decimal = round($decimal, 2); // por seguridad

            $fraccionesValidas = [0.00, 0.10, 0.20, 0.30, 0.50, 0.60, 0.70, 0.80, 0.90]; // En algunos POS se aceptan estas

            if (!in_array($decimal, $fraccionesValidas)) {
                throw new Exception(
                    "El monto ingresado por el cliente (S/ " . number_format($pagado, 2) .
                    ") no es válido. Las monedas válidas deben terminar en: " . implode(', ', $fraccionesValidas) .
                    ". Si está pagando con tarjeta, ingrese el monto exacto: S/ " . number_format($monto_importe_venta, 2)
                );
            }
        }
        /**
         * “Artículo 8°.- REQUISITOS DE LOS COMPROBANTES DE PAGO
         *Los comprobantes de pago tendrán los siguientes requisitos mínimos:
         *(…)
         *3. (…)
         *3.10. En los casos en que lo requiere el adquirente o el usuario o cuando el importe
         *total por boleta de venta supere la suma de setecientos soles (S/ 700.00), será necesario
         *consignar los siguientes datos de identificación del adquirente o usuario:
         *a) Apellidos y nombres.
         *b) Tipo y número de su documento de identidad
         */
        if ($tipoComprobante == '03' && $monto_importe_venta > 700 && !$cliente) {
            throw new Exception('Para boletas con monto mayor a S/ 700 debe agregar un cliente.');
        }

        self::validarFechaEmision($fechaEmision, $tipoComprobante);

        $negocioId = $data['negocio_id'] ?? null;
        $sucursalId = $data['sucursal_id'] ?? null;

        $negocio = Negocio::find($negocioId);
        if (!$negocio) {
            throw new Exception('El negocio ya no existe');
        }

        $modo_venta = $negocio->modo;
        return DB::transaction(function () use ($data, $modo_venta, $negocioId, $cliente, $venta, $tipoComprobante) {
            // Crear venta principal

            $nombreCliente = 'VARIOS'; //valores por defecto obligatorios por sunat

            if ($cliente) {
                $nombreCliente = $cliente['nombre_completo'];
            }

            $ventaModel = Venta::create([
                'uuid' => Str::uuid(),
                'cliente_id' => $cliente['id'] ?? null,
                'nombre_cliente' => $nombreCliente,
                'documento_cliente' => $cliente['numero_documento'] ?? '00000000', //valores por defecto obligatorios por sunat
                'tipo_documento_cliente' => $cliente['tipo_documento_id'] ?? '1', //valores por defecto obligatorios por sunat

                //Nuevos campos de dirección del cliente
                'cliente_ubigeo' => $cliente['ubigeo'] ?? null,
                'cliente_departamento' => $cliente['departamento'] ?? null,
                'cliente_provincia' => $cliente['provincia'] ?? null,
                'cliente_distrito' => $cliente['distrito'] ?? null,
                'cliente_urbanizacion' => $cliente['urbanizacion'] ?? null,
                'cliente_direccion' => $cliente['direccion'] ?? null,
                'cliente_email' => $cliente['email'] ?? null,
                'cliente_telefono' => $cliente['telefono'] ?? null,
                //'cliente_cod_local'=> $cliente['provincia'] ?? null,
                'modo_venta' => $modo_venta,
                'estado' => 'pagado',

                'valor_venta' => $venta['valor_venta'] ?? 0,
                'sub_total' => $venta['subtotal'] ?? 0,
                'redondeo' => $data['redondeo'] ?? 0,
                'monto_importe_venta' => $venta['monto_importe_venta'] ?? 0,

                'monto_operaciones_gravadas' => $venta['monto_operaciones_gravadas'] ?? 0,
                'monto_operaciones_exoneradas' => $venta['monto_operaciones_exoneradas'] ?? 0,
                'monto_operaciones_inafectas' => $venta['monto_operaciones_inafectas'] ?? 0,
                'monto_operaciones_exportacion' => $venta['monto_operaciones_exportacion'] ?? 0,
                'monto_operaciones_gratuitas' => $venta['monto_operaciones_gratuitas'] ?? 0,
                'monto_igv' => $venta['monto_igv'] ?? 0,
                'monto_igv_gratuito' => $venta['monto_igv_gratuito'] ?? 0,
                'icbper' => $venta['icbper'] ?? 0,
                'total_impuestos' => $venta['total_impuestos'] ?? 0,

                'tipo_comprobante_codigo' => $tipoComprobante,
                'serie_comprobante' => $data['serie_comprobante'] ?? null,
                'correlativo_comprobante' => $data['correlativo_comprobante'] ?? null,

                'sunat_comprobante_pdf' => null,
                'voucher_pdf' => null,
                'sunat_xml_firmado' => null,
                'sunat_cdr' => null,

                'caja_id' => $data['caja_id'] ?? null,
                'sucursal_id' => $data['sucursal_id'] ?? null,
                'fecha_emision' => $data['fecha_emision'] ?? null,
                'fecha_pago' => $data['fecha_pago'] ?? null,
                'negocio_id' => $negocioId,
                'tipo_factura' => '0101'
            ]);
            // Guardar métodos de pago
            VentaMetodoPago::where('venta_id', $ventaModel->id)->delete();
            foreach ($data['metodos_pagos'] ?? [] as $metodo) {
                VentaMetodoPago::create([
                    'venta_id' => $ventaModel->id,
                    'metodo' => $metodo['codigo'],
                    'monto' => $metodo['monto']
                ]);
            }

            // Guardar detalles de productos desde $venta['productos']
            foreach ($venta['productos'] as $detalle) {
                $cantidad = $detalle['cantidad'] ?? 1;
                $factor = $detalle['factor'] ?? 1;
                $totalUnidades = $cantidad * $factor;
                $detalleVenta = DetalleVenta::create([
                    'venta_id' => $ventaModel->id,
                    'producto_id' => $detalle['producto_id'] ?? null,
                    'unidad' => $detalle['unidad'] ?? null,
                    'descripcion' => $detalle['descripcion'] ?? null,
                    'cantidad' => $detalle['cantidad'] ?? 0,

                    'monto_valor_unitario' => $detalle['monto_valor_unitario'] ?? 0,
                    'monto_valor_gratuito' => $detalle['monto_valor_gratuito'] ?? 0,
                    'monto_valor_venta' => $detalle['monto_valor_venta'] ?? 0,
                    'monto_base_igv' => $detalle['monto_base_igv'] ?? 0,
                    'monto_precio_unitario' => $detalle['monto_precio_unitario'] ?? 0,

                    'porcentaje_igv' => $detalle['porcentaje_igv'] ?? 0,
                    'igv' => $detalle['igv'] ?? 0,
                    'tipo_afectacion_igv' => $detalle['tipo_afectacion_igv'] ?? null,
                    'total_impuestos' => $detalle['total_impuestos'] ?? 0,

                    'categoria_producto' => $detalle['categoria_producto'] ?? null,
                    'factor' => $detalle['factor'] ?? 1,
                    'es_gratuita' => $detalle['es_gratuita'] ?? false,
                    'es_icbper' => $detalle['es_icbper'] ?? false,
                    'icbper' => $detalle['icbper'] ?? 0,
                    'factor_icbper' => $detalle['factor_icbper'] ?? 0,
                ]);

                // === Crear salida y validar stock usando servicios ===
                $data = [
                    'producto_id' => $detalleVenta->producto_id,
                    'sucursal_id' => $ventaModel->sucursal_id,
                    'tipo_salida' => 'VENTA',
                    'cantidad' => $totalUnidades,
                    'costo_unitario' => 0, // se actualizará luego en validarStock()
                    'fecha_salida' => $ventaModel->fecha_emision,
                    'referencia_id' => $detalleVenta->id,
                    'referencia_tipo' => get_class($detalleVenta),
                    'created_by' => auth()->id(),
                    'estado' => 'pendiente',
                ];

                $salida = app(SalidaProductoServicio::class)->generarSalida($data);
                app(InventarioServicio::class)->validarStock($salida);
            }
            return $ventaModel;
        });
    }
    public static function registrar($data)
    {
        logger($data);
        $tipoComprobante = $data['tipo_comprobante_codigo'] ?? null;
        $sucursalId = $data['sucursal_id'] ?? null;
        $correlativoServicio = new CorrelativoServicio();
        if (!$sucursalId) {
            throw new Exception('No hay Sucursal Seleccionada.');
        }
        //revisamos el estado del negocio
        $sucursal = Sucursal::findOrFail($sucursalId);
        $modo = $sucursal->negocio->modo;

        $numeracion = $correlativoServicio->obtenerNumeracion($sucursalId, $tipoComprobante);
        // throw new Exception(json_encode($data));

        $ventaModel = self::generarVenta($data);

        $tipoComprobante = $data['tipo_comprobante_codigo'] ?? null;
        if ($tipoComprobante == 'ticket') {
            // Para tickets no se requiere tipo de comprobante específico, se va a facturar o boletear despues
            //Generar Nota de venta simple
            ComprobanteSinSunatServicio::generarTicket($ventaModel->id, $numeracion);

        } else {
            $comprobanteServicio = new ComprobanteServicio();
            $comprobanteServicio->generar($ventaModel->id, $numeracion);
        }

        if ($modo == 'produccion') {
            $correlativoServicio->guardarCorrelativo();
        }

        $ventaConRelaciones = Venta::with(['detalles.producto', 'cliente', 'notas'])
            ->find($ventaModel->id);

        // Agrega URLs si las necesitas
        $ventaConRelaciones->voucher_pdf = $ventaConRelaciones->voucher_pdf ? Storage::disk('public')->url($ventaConRelaciones->voucher_pdf) : null;
        $ventaConRelaciones->sunat_comprobante_pdf = $ventaConRelaciones->sunat_comprobante_pdf ? Storage::disk('public')->url($ventaConRelaciones->sunat_comprobante_pdf) : null;
        $ventaConRelaciones->sunat_xml_firmado = $ventaConRelaciones->sunat_xml_firmado ? Storage::disk('public')->url($ventaConRelaciones->sunat_xml_firmado) : null;
        $ventaConRelaciones->sunat_cdr = $ventaConRelaciones->sunat_cdr ? Storage::disk('public')->url($ventaConRelaciones->sunat_cdr) : null;

        return $ventaConRelaciones;

    }
    public static function listar($negocio, $sucursal = null, $take = 10)
    {
        $user = Auth::user();

        if ($user->hasRole('dueno_tienda')) {
            $duenoTiendaId = $user->id;
        } elseif ($user->hasRole('vendedor')) {
            throw new Exception("Aún no habilitado para cuenta vendedor.");
        } else {
            throw new Exception("Usuario no autorizado realizar esta acción.");
        }

        // Validar que el usuario tenga acceso al negocio
        $negocioIds = $user->negocios->pluck('id')->toArray();
        if (!in_array($negocio, $negocioIds)) {
            throw new Exception("Usuario no autorizado para este negocio: {$negocio}");
        }

        // Si el negocio tiene sucursales, validar acceso
        $sucursalesUsuario = $user->sucursales->pluck('id')->toArray();

        if ($sucursal && !in_array($sucursal, $sucursalesUsuario)) {
            throw new Exception("Usuario no autorizado para la sucursal {$sucursal} del negocio {$negocio}");
        }

        // Query base
        $query = Venta::where('negocio_id', $negocio)
            ->with(['detalles', 'cliente', 'notas']);

        if ($sucursal) {
            $query->where('sucursal_id', $sucursal);
        } else {
            $query->whereNull('sucursal_id');
        }

        $ventas = $query->orderByDesc('fecha_emision')
            ->orderByDesc('created_at')
            ->take($take)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($venta) {
                $venta->voucher_pdf = $venta->voucher_pdf ? Storage::disk('public')->url($venta->voucher_pdf) : null;
                $venta->sunat_comprobante_pdf = $venta->sunat_comprobante_pdf ? Storage::disk('public')->url($venta->sunat_comprobante_pdf) : null;
                $venta->sunat_xml_firmado = $venta->sunat_xml_firmado ? Storage::disk('public')->url($venta->sunat_xml_firmado) : null;
                $venta->sunat_cdr = $venta->sunat_cdr ? Storage::disk('public')->url($venta->sunat_cdr) : null;
                return $venta;
            });

        return $ventas;
    }

    public static function getFactorIcbper($fechaEmision): float
    {
        $anio = (int) $fechaEmision->format('Y');
        switch ($anio) {
            case 2019:
                return 0.10;
            case 2020:
                return 0.20;
            case 2021:
                return 0.30;
            case 2022:
                return 0.40;
            default:
                return 0.50; // Para 2023 en adelante
        }
    }

    /**
     * Validación de la fecha de emisión del comprobante
     * 
     * - Solo se aplica si el tipo de comprobante es factura (01) o boleta (03).
     * - La fecha debe existir y ser válida.
     * - Para factura (01), la fecha de emisión debe estar dentro de los últimos 3 días según normativas SUNAT.
     * 
     * @param string|null $fechaEmision Fecha de emisión en formato Y-m-d
     * @param string|null $tipoComprobante Código del tipo de comprobante ('01' = factura, '03' = boleta, etc.)
     * @throws Exception Si la validación falla
     */
    private static function validarFechaEmision(?string $fechaEmision, ?string $tipoComprobante)
    {
        if ($tipoComprobante === '01' || $tipoComprobante === '03') {
            if (empty($fechaEmision) || !strtotime($fechaEmision)) {
                throw new Exception('Debe ingresar una fecha de emisión válida.');
            }

            $fechaEmisionDate = new \DateTime($fechaEmision);
            $fechaLimite = (new \DateTime())->modify('-3 days');

            if ($fechaEmisionDate < $fechaLimite) {
                throw new Exception('La fecha de emisión de la factura no puede ser mayor a 3 días antes de la fecha actual.');
            }

        }
    }

}
