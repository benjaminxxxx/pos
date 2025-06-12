<?php
// app/Services/VentaServicio.php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaMetodoPago;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class VentaServicio
{
    public static function generarVenta($data)
    {
        $venta = $data['venta'];
        $tipoComprobante = $data['tipo_comprobante_codigo'] ?? null;
        $cliente = $data['cliente'] ?? null;
        $fechaEmision = $data['fecha_emision'] ?? null;


        if (!$tipoComprobante || !in_array($tipoComprobante, ['01', '03', 'ticket'])) {
            throw new Exception('Debe seleccionar un tipo de comprobante.');
        }
        if ($tipoComprobante == 'ticket') {
            $tipoComprobante = null; // Para tickets no se requiere tipo de comprobante específico, se va a facturar o boletear despues
        }
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

        $sucursal = Sucursal::find($data['sucursal_id']);
        if (!$sucursal) {
            throw new Exception('La sucursal ya no existe');
        }

        $modo_venta = $sucursal->modo_venta;
        $negocio_id = $sucursal->negocio_id;
        //throw new Exception($modo_venta . ' - ' . $negocio_id);
        return DB::transaction(function () use ($data, $modo_venta, $negocio_id, $cliente, $venta, $tipoComprobante) {
            // Crear venta principal

            $nombreCliente = 'VARIOS'; //valores por defecto obligatorios por sunat

            if ($cliente) {
                $nombreCliente = $cliente['tipo_cliente_id'] === 'empresa'
                    ? $cliente['nombre_comercial']
                    : $cliente['nombre_completo'];
            }

            $ventaModel = Venta::create([
                'uuid' => Str::uuid(),
                'cliente_id' => $cliente['id'] ?? null,
                'nombre_cliente' => $nombreCliente,
                'documento_cliente' => $cliente['numero_documento'] ?? '00000000', //valores por defecto obligatorios por sunat
                'tipo_documento_cliente' => $cliente['tipo_documento_id'] ?? '1', //valores por defecto obligatorios por sunat
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
                'negocio_id' => $negocio_id,
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
                DetalleVenta::create([
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
            }



            return $ventaModel;
        });
    }
    public static function registrar($data)
    {
        logger($data);

        $ventaModel = self::generarVenta($data);

        $comprobanteServicio = new ComprobanteServicio();
        $comprobanteServicio->generar($ventaModel->id);

        $ventaConRelaciones = Venta::with(['detalles.producto', 'cliente', 'notas'])
            ->find($ventaModel->id);

        // Agrega URLs si las necesitas
        $ventaConRelaciones->voucher_pdf = $ventaConRelaciones->voucher_pdf ? Storage::disk('public')->url($ventaConRelaciones->voucher_pdf) : null;
        $ventaConRelaciones->sunat_comprobante_pdf = $ventaConRelaciones->sunat_comprobante_pdf ? Storage::disk('public')->url($ventaConRelaciones->sunat_comprobante_pdf) : null;
        $ventaConRelaciones->sunat_xml_firmado = $ventaConRelaciones->sunat_xml_firmado ? Storage::disk('public')->url($ventaConRelaciones->sunat_xml_firmado) : null;
        $ventaConRelaciones->sunat_cdr = $ventaConRelaciones->sunat_cdr ? Storage::disk('public')->url($ventaConRelaciones->sunat_cdr) : null;


        return $ventaConRelaciones;

    }
    public static function listar($sucursal, $take = 10)
    {
        $user = Auth::user();
        $duenoTiendaId = null;

        if ($user->hasRole('dueno_tienda')) {
            $duenoTiendaId = $user->id;
        } elseif ($user->hasRole('vendedor')) {
            // Suponiendo que el vendedor tiene una relación belongsTo hacia el dueño de la tienda
            throw new Exception("Apun no habillitado para vendedor account");

            //$duenoTiendaId = $user->dueno_tienda_id; // funcion por crear aun no existe
        } else {
            throw new Exception("Usuario no autorizado realizar esta accion.");

        }
        $sucursales = $user->sucursales->pluck('id')->toArray();
        if (in_array($sucursal, $sucursales)) {
            $ventas = Venta::where('sucursal_id', $sucursal)
                ->with(['detalles', 'cliente', 'notas'])
                ->take($take)
                ->get()
                ->map(function ($venta) {
                    $venta->voucher_pdf = $venta->voucher_pdf ? Storage::disk('public')->url($venta->voucher_pdf) : null;
                    $venta->sunat_comprobante_pdf = $venta->sunat_comprobante_pdf ? Storage::disk('public')->url($venta->sunat_comprobante_pdf) : null;
                    $venta->sunat_xml_firmado = $venta->sunat_xml_firmado ? Storage::disk('public')->url($venta->sunat_xml_firmado) : null;
                    $venta->sunat_cdr = $venta->sunat_cdr ? Storage::disk('public')->url($venta->sunat_cdr) : null;
                    return $venta;
                });

            return $ventas;
        } else {
            throw new Exception("Usuario no autorizado para esta sucursal.");
        }
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
