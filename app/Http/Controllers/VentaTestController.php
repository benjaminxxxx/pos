<?php
// VentaTestController.php
namespace App\Http\Controllers;

use App\Services\VentaServicio;
use App\Models\Venta;

class VentaTestController extends Controller
{
    public function testear()
    {
        $data = [
            "metodos_pagos" => [
                ["codigo" => "cash", "monto" => "100.00"],
                ["codigo" => "yape", "monto" => "13.18"],
            ],
            "cliente" => [
                "id"                => 5,
                "nombre_completo"   => "inversiones yarech EIRL",
                "tipo_documento_id" => "6",
                "numero_documento"  => "20611263300",
                "telefono"          => "953294649",
                "direccion"         => "AV. LAS FLORES MZ 17 LT 4 A. H. LAS FLORES",
                "departamento"      => "AREQUIPA",
                "provincia"         => "CARAVELI",
                "email"             => "RONALDPALLI81@GMAIL.COM",
                "distrito"          => "CHALA",
            ],
            "venta" => [
                "monto_operaciones_gravadas"    => 95.92,
                "monto_operaciones_exoneradas"  => 0,
                "monto_operaciones_inafectas"   => 0,
                "monto_operaciones_exportacion" => 0,
                "monto_operaciones_gratuitas"   => 0,
                "monto_igv"                     => 17.26,
                "monto_igv_gratuito"            => 0,
                "icbper"                        => 0,
                "total_impuestos"               => 17.26,
                "valor_venta"                   => 95.92,
                "subtotal"                      => 113.18,
                "monto_importe_venta"           => 113.18,
                "redondeo"                      => 0,
                "productos" => [
                    [
                        "producto_id"           => 3,
                        "descripcion"           => "Trapo industrial Cosido de color",
                        "unidad"                => "NIU",
                        "factor"                => 1,
                        "categoria_producto"    => null,
                        "cantidad"              => 1,
                        "monto_valor_unitario"  => 3.135593,
                        "monto_valor_gratuito"  => 0,
                        "monto_valor_venta"     => 3.14,
                        "monto_base_igv"        => 3.14,
                        "monto_precio_unitario" => "3.70",
                        "porcentaje_igv"        => 18,
                        "igv"                   => 0.56,
                        "tipo_afectacion_igv"   => "10",
                        "total_impuestos"       => 0.56,
                        "es_gratuita"           => false,
                        "es_icbper"             => false,
                        "icbper"                => 0,
                        "factor_icbper"         => 0,
                    ],
                    [
                        "producto_id"           => 4,
                        "descripcion"           => "Broca de 34MM",
                        "unidad"                => "NIU",
                        "factor"                => 1,
                        "categoria_producto"    => null,
                        "cantidad"              => 2,
                        "monto_valor_unitario"  => 43,
                        "monto_valor_gratuito"  => 0,
                        "monto_valor_venta"     => 86,
                        "monto_base_igv"        => 86,
                        "monto_precio_unitario" => "50.74",
                        "porcentaje_igv"        => 18,
                        "igv"                   => 15.48,
                        "tipo_afectacion_igv"   => "10",
                        "total_impuestos"       => 15.48,
                        "es_gratuita"           => false,
                        "es_icbper"             => false,
                        "icbper"                => 0,
                        "factor_icbper"         => 0,
                    ],
                    [
                        "producto_id"           => 24,
                        "descripcion"           => "Pintura en Spray C&A Normal Amarillo Limon",
                        "unidad"                => "NIU",
                        "factor"                => 1,
                        "categoria_producto"    => null,
                        "cantidad"              => 1,
                        "monto_valor_unitario"  => 6.779661,
                        "monto_valor_gratuito"  => 0,
                        "monto_valor_venta"     => 6.78,
                        "monto_base_igv"        => 6.78,
                        "monto_precio_unitario" => "8.00",
                        "porcentaje_igv"        => 18,
                        "igv"                   => 1.22,
                        "tipo_afectacion_igv"   => "10",
                        "total_impuestos"       => 1.22,
                        "es_gratuita"           => false,
                        "es_icbper"             => false,
                        "icbper"                => 0,
                        "factor_icbper"         => 0,
                    ],
                ],
            ],
            "tipo_comprobante_codigo" => "01",
            "sucursal_id"             => 2,
            "fecha_emision"           => now()->format('Y-m-d'),
            "totalPago"               => "113.18",
            "caja_id"                 => null,
        ];

        try {

            // No existe, crear nueva
            $venta = VentaServicio::registrarv2($data);

            return response()->json([
                'success'    => true,
                'accion'     => 'nueva_venta',
                'venta_id'   => $venta->id,
                'serie'      => $venta->serie_comprobante,
                'correlativo'=> $venta->correlativo_comprobante,
                'estado'     => $venta->sunat_estado,
                'cdr_codigo' => $venta->sunat_cdr_codigo,
                'cdr_desc'   => $venta->sunat_cdr_descripcion,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'linea'   => $e->getLine(),
                'archivo' => $e->getFile(),
            ]);
        }
    }
}