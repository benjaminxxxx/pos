<?php

namespace App\Services;

use App\Models\Negocio;
use App\Models\Nota;
use App\Models\SucursalCorrelativo;
use App\Models\Unidad;
use App\Models\Venta;
use App\Services\Facturacion\PdfGenerators\A4VoucherGenerator;
use Exception;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Luecano\NumeroALetras\NumeroALetras;
use Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

class ComprobanteSinSunatServicio
{
    public function __construct()
    {
       
    }

    public static function generarTicket($ventaId,$numeracion)
    {
        $venta = Venta::findOrFail($ventaId);
      
        $serie = $numeracion['serie'];
        $correlativo = $numeracion['correlativo'];
        //$sunatComprobantePdf = A4VoucherGenerator::generarDocumento($invoice, $negocio);
        $voucherPdf = VoucherServicio::generarDocumento($venta, $serie, $correlativo);

        // Actualizar venta
        //$venta->sunat_comprobante_pdf = $sunatComprobantePdf;
        $venta->voucher_pdf = $voucherPdf;
        $venta->serie_comprobante = $serie;
        $venta->correlativo_comprobante = $correlativo;
        $venta->save();

    }
}
