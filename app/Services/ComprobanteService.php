<?php

namespace App\Services;

use App\Models\Correlativo;
use App\Models\Negocio;
use App\Models\InvoiceExtraInformation;
use App\Models\SiteConfig;
use App\Models\Venta;
use Auth;
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
use Illuminate\Support\Carbon;
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

        $comprobanteServicio = new ComprobanteServicio();
        $fecha = Carbon::now();
        $servicio = $comprobanteServicio->generar($ventaId,'factura',$fecha);

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