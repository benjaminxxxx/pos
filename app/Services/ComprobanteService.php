<?php

namespace App\Services;

use App\Models\Correlativo;
use App\Models\Negocio;
use App\Models\InvoiceExtraInformation;
use App\Models\SiteConfig;
use App\Models\Venta;
use DateTime;
use Exception;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Storage;

class ComprobanteService
{
    public $compania;
    public $venta;
    public function __construct()
    {
        $this->sunatService = new SunatService();
    }
    public static function generar($ventaId)
    {

        $venta = Venta::find($ventaId);
        $correlativo = null;

        if (!$venta) {
            throw new Exception("La venta proporcionada no existe");
        }

        $negocio = $venta->negocio;
        if (!$negocio) {
            throw new Exception("La venta proporcionada no está asociado a ningún negocio");
        }

        $serieComprobante = $venta->serie_comprobante;
        $correlativoComprobante = $venta->correlativo_comprobante;

        if (!$venta->serie_comprobante || !$venta->correlativo_comprobante) {
            $correlativo = self::generarSiguienteCorrelativo($venta->modo_venta, $venta->tipo_comprobante_codigo, $venta->sucursal_id);
            $serieComprobante = $correlativo['serie_comprobante'];
            $correlativoComprobante = $correlativo['correlativo_comprobante'];
        }
        if (!$serieComprobante || !$correlativoComprobante) {
            throw new Exception("La correlativos no estan correctamente configurados");
        }

        $tipoDocumento = $venta->tipo_documento;
        $fechaEmision = $venta->fecha_emision;
        $montoOperacionesGravadas = $venta->monto_operaciones_gravadas;
        $montoOperacionesExoneradas = $venta->monto_operaciones_exoneradas;
        $montoOperacionesInafectas = $venta->monto_operaciones_inafectas;
        $montoOperacionesExportacion = $venta->monto_operaciones_exportacion;
        $montoOperacionesGratuitas = $venta->monto_operaciones_gratuitas;

        
        $totalImpuestos = $mtoIGV + $mtoIGVGratuitas + $icbper;

        $data = [
            "ublVersion" => "2.1",
            "tipoDoc" => $tipoDocumento,
            "tipoOperacion" => "0101",
            "serie" => $serieComprobante,
            "correlativo" => $correlativoComprobante,
            "fechaEmision" => $fechaEmision,
            "formaPago" => [
                "moneda" => "PEN",
                "tipo" => "Contado"
            ],
            "tipoMoneda" => "PEN",
            "company" => [
                "ruc" => 20611263300,
                "razonSocial" => "INVERSIONES YARECH S.R.L.",
                "nombreComercial" => "-",
                "address" => [
                    "ubigueo" => "040307",
                    "departamento" => "AREQUIPA",
                    "provincia" => "CARAVELI",
                    "distrito" => "CHALA",
                    "urbanizacion" => "-",
                    "direccion" => "AV. LAS FLORES MZA. 17 LOTE. 4 A.H.  FLORES",
                    "codLocal" => "0000"
                ]
            ],

            "client" => [
                "tipoDoc" => '1',
                "numDoc" => '00000000',
                "rznSocial" => 'VARIOS'
            ],
            //Mto Operaciones
            "mtoOperGravadas" => $montoOperacionesGravadas,
            "mtoOperExoneradas" => $montoOperacionesExoneradas,
            "mtoOperInafectas" => $montoOperacionesInafectas,
            "mtoOperExportacion" => $montoOperacionesExportacion,
            "mtoOperGratuitas" => $montoOperacionesGratuitas,

            //Impuestos
            "mtoIGV" => (float) $venta->igv,
            "mtoIGVGratuitas" => (float) $venta->igv,
            "icbper" => (float) $venta->igv,
            "totalImpuestos" => $totalImpuestos,

            /*
            valorVenta	Total sin impuestos
            subTotal	valorVenta + IGV + ICBPER
            redondeo	Ajuste decimal para llegar al monto deseado
            mtoImpVenta	Total final a pagar (subTotal + redondeo)
            */
            "valorVenta" => (float) $sale->subtotal,
            "subTotal" => (float) $sale->total_amount,
            "redondeo"=> (float) $sale->total_tax,
            "mtoImpVenta" => (float) $sale->total_amount,
            "details" => $details,
            "legends" => [
                [
                    "code" => "1000",
                    "value" => ""
                ]
            ]
        ];

    }
    private static function generarSiguienteCorrelativo($modoVenta, $tipo_comprobante_codigo, $sucursal_id)
    {
        $correlativoResponse = [
            'serie_comprobante' => null,
            'correlativo_comprobante' => null,
        ];
        if ($modoVenta == 'produccion') {
            $correlativo = Correlativo::where('tipo_comprobante_codigo', $tipo_comprobante_codigo)
                ->where('estado', true)
                ->whereHas('sucursales', function ($query) use ($sucursal_id) {
                    $query->where('sucursales.id', $sucursal_id);
                })
                ->first();
            if ($correlativo) {
                $correlativoResponse['serie_comprobante'] = $correlativo->serie;
                $correlativoResponse['correlativo_comprobante'] = $correlativo->correlativo_actual;
            }
        }elseif($modoVenta == 'desarrollo'){

            $prefijo = match ($tipo_comprobante_codigo) {
                '01' => 'F',   // Factura
                '03' => 'B',   // Boleta
                '09' => 'T',   // Guía Remisión
                default => 'X',
            };
            
            $serie = $prefijo . str_pad(1, 3, '0', STR_PAD_LEFT); 
            $correlativoResponse['serie_comprobante'] = $serie;
            $correlativoResponse['correlativo_comprobante'] = '1';

            $ultimaVenta = Venta::where('tipo_comprobante_codigo', $tipo_comprobante_codigo)->latest()->first();
            if($ultimaVenta){
                $correlativoResponse['serie_comprobante'] = $ultimaVenta->serie_comprobante?$ultimaVenta->serie_comprobante:$correlativoResponse['serie_comprobante'];
                $correlativoResponse['correlativo_comprobante'] = (int)$ultimaVenta->correlativo_comprobante+1;
            }
        }
        return $correlativoResponse;
    }
}