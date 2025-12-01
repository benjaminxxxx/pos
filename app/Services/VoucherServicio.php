<?php

namespace App\Services;

use App\Models\DisenioDisponible;
use App\Models\DisenioImpresion;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;

class VoucherServicio
{
    public static function generarDocumento($venta, $serie, $correlativo)
    {
        $textDocument = match ($venta->tipo_comprobante_codigo) {
            '01' => 'FACTURA ELECTRÓNICA',
            '03' => 'BOLETA DE VENTA ELECTRÓNICA',
            '07' => 'NOTA DE CRÉDITO',
            '08' => 'NOTA DE DÉBITO',
            '20' => 'COMPROBANTE DE RETENCIÓN',
            '40' => 'RECIBO DE SERVICIOS PÚBLICOS',
            'ticket' => 'NOTA DE VENTA', // No valido para comprobantes oficiales
            default => 'DOCUMENTO ELECTRÓNICO',
        };

        $ruc = $venta->negocio->ruc ?? '00000000000';
        $fechaEmision = \Carbon\Carbon::parse($venta->fecha_emision)->format('Y-m-d'); // Asegura el formato

        $tipoDocCliente = $venta->tipo_documento_cliente ?? '0'; // '6' = RUC, '1' = DNI, etc.
        $numDocCliente = $venta->documento_cliente ?? '-';

        $igv = number_format((float) $venta->monto_igv, 2, '.', '');
        $total = number_format((float) $venta->monto_importe_venta, 2, '.', '');

        $qrData = "{$ruc}|{$venta->tipo_comprobante_codigo}|{$serie}|{$correlativo}|{$igv}|{$total}|{$fechaEmision}|{$tipoDocCliente}|{$numDocCliente}";

        $renderer = new ImageRenderer(
            new RendererStyle(100, 0),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        $qrSvg = $writer->writeString($qrData, 'UTF-8', ErrorCorrectionLevel::Q());

        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        $metodosPago = $venta->metodosPago; //venta_id, metodo, monto
        $arrayMetodosPago = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'yape' => 'Yape',
            'plin' => 'Plin',
        ];

        $metodosEnVentaString = collect($metodosPago)
            ->map(fn($m) => $arrayMetodosPago[$m->metodo] ?? null) // traduce
            ->filter() // elimina nulos
            ->implode(', '); // concatena

        $informacion = [
            'Cabecera' => [],
            'Centro' => [],
            'Pie' => [],
        ];
        foreach ($venta->negocio->informacionAdicional as $info) {
            $informacion[$info->ubicacion][] = [
                'clave' => $info->clave,
                'valor' => $info->valor,
            ];
        }

        $logoPath = $venta->negocio->logo_factura
            ? Storage::disk('public')->path($venta->negocio->logo_factura)
            : null;

        $data = [
            'qrBase64' => $qrBase64, // Código QR generado
            'logoBase64' => $logoPath
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : null,
            // Datos de empresa (asumimos que existe relación `negocio`)
            'nombre_legal' => $venta->negocio->nombre_legal ?? 'Empresa',
            'nombre_comercial' => $venta->negocio->nombre_comercial ?? 'Nombre Comercial',
            'ruc' => $ruc,
            'direccion' => $venta->negocio->direccion ?? 'Dirección no disponible',

            // Tipo de documento (boleta o factura según tipo_factura)
            'text_document' => $textDocument,
            'tipo_comprobante_codigo' => $venta->tipo_comprobante_codigo,

            // Fechas
            'fecha_emision' => $venta->fecha_emision,
            //'fecha_pago' => $venta->fecha_pago,

            // Datos del cliente
            'documento_cliente' => $venta->documento_cliente,
            'nombre_cliente' => $venta->nombre_cliente,
            //'tipo_documento_cliente' => $venta->tipo_documento_cliente,
            //'cliente_direccion' => $venta->cliente->direccion ?? 'No especificada',

            // Comprobante
            //'tipo_comprobante_codigo' => $venta->tipo_comprobante_codigo,
            'serie_comprobante' => $serie,
            'correlativo_comprobante' => str_pad($correlativo, 6, '0', STR_PAD_LEFT),

            // Montos operativos
            'monto_operaciones_gravadas' => (float) $venta->monto_operaciones_gravadas,
            'monto_operaciones_exoneradas' => (float) $venta->monto_operaciones_exoneradas,
            'monto_operaciones_inafectas' => (float) $venta->monto_operaciones_inafectas,
            'monto_operaciones_exportacion' => (float) $venta->monto_operaciones_exportacion,
            'monto_operaciones_gratuitas' => (float) $venta->monto_operaciones_gratuitas,

            // Impuestos
            'monto_igv' => (float) $venta->monto_igv,
            'monto_igv_gratuito' => (float) $venta->monto_igv_gratuito,
            'icbper' => (float) $venta->icbper,
            'total_impuestos' => (float) $venta->total_impuestos,

            // Totales
            'valor_venta' => (float) $venta->valor_venta,
            'sub_total' => (float) $venta->sub_total,
            'redondeo' => (float) $venta->redondeo,
            'monto_importe_venta' => (float) $venta->monto_importe_venta,

            // Tipo de pago
            'estado' => $venta->estado,
            'modo_venta' => $venta->modo_venta,

            'modalidad_pago' => $metodosEnVentaString,
            'informacion' => $informacion,
            'texto_total_letras' => self::montoEnLetras($venta->monto_importe_venta ?? 0),

            // Detalles
            'items' => $venta->detalles->map(function ($detalle) {
                return [
                    'codigo' => 'P' . str_pad($detalle->producto_id, 5, '0', STR_PAD_LEFT),
                    'producto_id' => $detalle->producto_id,
                    'descripcion' => $detalle->descripcion,
                    'unidad' => $detalle->unidad,
                    'cantidad' => (float) $detalle->cantidad,
                    'monto_valor_unitario' => (float) $detalle->monto_valor_unitario,
                    'monto_valor_gratuito' => (float) $detalle->monto_valor_gratuito,
                    'monto_valor_venta' => (float) $detalle->monto_valor_venta,
                    'monto_base_igv' => (float) $detalle->monto_base_igv,
                    'porcentaje_igv' => (float) $detalle->porcentaje_igv,
                    'igv' => (float) $detalle->igv,
                    'tipo_afectacion_igv' => $detalle->tipo_afectacion_igv,
                    'total_impuestos' => (float) $detalle->total_impuestos,
                    'monto_precio_unitario' => (float) $detalle->monto_precio_unitario,
                    'categoria_producto' => $detalle->categoria_producto,
                    'factor' => (float) $detalle->factor,
                    'es_gratuita' => (bool) $detalle->es_gratuita,
                    'es_icbper' => (bool) $detalle->es_icbper,
                    'icbper' => (float) $detalle->icbper,
                    'factor_icbper' => (float) $detalle->factor_icbper,
                ];
            })->toArray(),
        ];
        /*
                $disenioImpresion = DisenioImpresion::with('disenioDisponible')
                    ->where('negocio_id', $venta->negocio_id)
                    ->where('sucursal_id', $venta->sucursal_id)
                    ->whereHas('disenioDisponible', function ($query) use ($venta) {
                        $query->where('tipo_comprobante_codigo', $venta->tipo_comprobante_codigo);
                    })
                    ->first();
                //throw new Exception($venta->negocio_id .' - ' .   $venta->sucursal_id .' - ' .  $venta->tipo_comprobante_codigo , 2);
                $view = 'documents.boleta'; // Vista por defecto
                $width = 80 / 25.4 * 72; // Convertir 80 mm a puntos
                $height = 200 / 25.4 * 72; // Longitud de 300 mm convertida a puntos (ajústala según la necesidad)
                $orientation = 'portrait';
                if ($disenioImpresion && $disenioImpresion->disenioDisponible) {
                    $width = $disenioImpresion->disenioDisponible->width_mm / 25.4 * 72; // Convertir mm a puntos
                    $height = $disenioImpresion->disenioDisponible->height_mm / 25.4 * 72; // Convertir mm a puntos

                    $codigo = $disenioImpresion->disenioDisponible->codigo;
                    $view = "documents.{$venta->tipo_comprobante_codigo}.{$codigo}";
                    $orientation = 'landscape';
                }

                $pdf = Pdf::loadView($view, $data);


                $pdf->setPaper([0, 0, $width, $height],  $orientation);


                $folder = date('Y') . '/' . date('m'); // ejemplo: 2025/09
                $filename = 'voucher_' . time() . '.pdf';
                $path = $folder . '/' . $filename;

                Storage::disk('public')->put($path, $pdf->output());

                return $path;*/
        // ===========================================================
// 1) BUSCAR DISEÑO ACTIVO DE IMPRESIÓN
// ===========================================================
        $disenioImpresion = DisenioImpresion::with('disenioDisponible')
            ->where('negocio_id', $venta->negocio_id)
            ->where('sucursal_id', $venta->sucursal_id)
            ->where('activo', true)
            ->whereHas('disenioDisponible', function ($query) use ($venta) {
                $query->where('tipo_comprobante_codigo', $venta->tipo_comprobante_codigo);
            })
            ->first();

        // Diseño base
        $disenioBase = DisenioDisponible::where('tipo_comprobante_codigo', $venta->tipo_comprobante_codigo)->first();

        // Valores iniciales (fallback base → estáticos)
        $width_mm = $disenioBase?->base_width_mm ?? 80;
        $height_mm = $disenioBase?->base_height_mm ?? 200;
        $orientation = $disenioBase?->base_orientation ?? 'portrait';
        $margin_top_mm = $disenioBase?->base_margin_top_mm ?? 0;
        $margin_bottom_mm = $disenioBase?->base_margin_bottom_mm ?? 0;
        $margin_left_mm = $disenioBase?->base_margin_left_mm ?? 0;
        $margin_right_mm = $disenioBase?->base_margin_right_mm ?? 0;

        $codigoVista = $disenioBase?->codigo ?? 'default';

        // ===========================================================
// 2) SI HAY DISEÑO ACTIVO → SOBRESCRIBIR
// ===========================================================
        if ($disenioImpresion) {
            $width_mm = $disenioImpresion->custom_width_mm ?? $width_mm;
            $height_mm = $disenioImpresion->custom_height_mm ?? $height_mm;
            $orientation = $disenioImpresion->custom_orientation ?? $orientation;
            $margin_top_mm = $disenioImpresion->custom_margin_top_mm ?? $margin_top_mm;
            $margin_bottom_mm = $disenioImpresion->custom_margin_bottom_mm ?? $margin_bottom_mm;
            $margin_left_mm = $disenioImpresion->custom_margin_left_mm ?? $margin_left_mm;
            $margin_right_mm = $disenioImpresion->custom_margin_right_mm ?? $margin_right_mm;

            // El código de vista del diseño activo
            $codigoVista = $disenioImpresion->disenioDisponible?->codigo ?? $codigoVista;
        }

        // ===========================================================
// 3) CONVERSIÓN mm → puntos PDF
// ===========================================================
        $width = $width_mm / 25.4 * 72;
        $height = $height_mm / 25.4 * 72;

        // ===========================================================
// 4) DEFINIR LA VISTA
// ===========================================================
        $view = "documents.boleta";
        $tipo = $venta->tipo_comprobante_codigo;
        if ($disenioImpresion) {

            $view = "documents.{$tipo}.{$codigoVista}";
        }



        // ===========================================================
// 5) GENERAR PDF
// ===========================================================
        $pdf = Pdf::loadView($view, [
            ...$data,
            'margin_top_mm' => $margin_top_mm,
            'margin_bottom_mm' => $margin_bottom_mm,
            'margin_left_mm' => $margin_left_mm,
            'margin_right_mm' => $margin_right_mm,
        ]);

        $pdf->setPaper([0, 0, $width, $height], $orientation);
        /*
                $options = [
                    'margin-top' => $margin_top_mm,
                    'margin-bottom' => $margin_bottom_mm,
                    'margin-left' => $margin_left_mm,
                    'margin-right' => $margin_right_mm,
                ];
                throw new Exception(json_encode($options), 1);
                $pdf->setOptions($options);
        */
        // ===========================================================
// 6) GUARDAR PDF
// ===========================================================
        $folder = date('Y') . '/' . date('m');
        $filename = 'voucher_' . time() . '.pdf';
        $path = $folder . '/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;

    }
    protected static function montoEnLetras($monto)
    {
        $formatter = new NumeroALetras();
        return $formatter->toInvoice($monto, 2, 'SOLES');
    }
}