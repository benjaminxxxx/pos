<?php

namespace App\Services\Facturacion\PdfGenerators;

use Dompdf\Dompdf;
use Dompdf\Options;
use Greenter\Report\HtmlReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Illuminate\Support\Facades\Storage;

class A4VoucherGenerator
{
    public static function generarDocumento($invoice, $negocio)
    {

        $logo_factura = $negocio->logo_factura;

        $headers = [
            'logo' => $logo_factura,
            'informacion_adicional' => $negocio->informacionAdicional,
        ];
        $htmlInvoice = self::getHtmlReport($invoice, $headers);
        $pdfPath = self::generateInvoicePdf($htmlInvoice, $invoice->getName());
        return $pdfPath;
    }
    public static function generateInvoicePdf(string $html, string $invoiceName): string
    {
        // Configura Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Configura el tamaño de papel y la orientación
        $dompdf->setPaper('A4', 'portrait');

        // Renderiza el PDF
        $dompdf->render();

        // Obtiene el contenido del PDF
        $output = $dompdf->output();

        // Genera la ruta para almacenar el archivo
        $filePath = date('Y/m') . '/' . $invoiceName . '.pdf';

        // Guarda el archivo en el disco público
        Storage::disk('public')->put($filePath, $output);

        // Retorna la URL relativa del archivo guardado
        return $filePath;
    }
    public static function getHtmlReport($invoice, $headers)
    {
        $templatePath = resource_path('views/documents');
        $twigOptions = [
            'cache' => storage_path('app/cache/twig'), // Directorio donde guardar la caché de Twig
            'strict_variables' => true,
        ];

        $report = new HtmlReport($templatePath, $twigOptions);
        $resolver = new DefaultTemplateResolver();
        //$report->setTemplate('factura.html.twig'); funcionaba 

        //$resolver = new DefaultTemplateResolver();
        $report->setTemplate($resolver->getTemplate($invoice));

        $ruc = $invoice->getCompany()->getRuc();
        $logoPath = $headers['logo'] ?? null;
        $logo = $logoPath ? Storage::disk('public')->get($logoPath) : '';

        $userData = [];
        $informations = $headers['informacion_adicional'] ?? [];

        // Inicializa los valores para el 'header', 'extras', y 'footer' vacíos
        $headerText = '';
        $footerText = '';
        $extrasArray = [];
        foreach ($informations as $info) {
            if ($info->ubicacion === 'Cabecera') {
                $headerText .= $info->clave . ': <b>' . $info->valor . '</b><br/>';
            } elseif ($info->ubicacion === 'Pie') {
                $footerText .= $info->clave . ': <b>' . $info->valor . '</b><br/>';
            } elseif ($info->ubicacion === 'Centro') {
                $extrasArray[] = [
                    'name' => $info->clave,
                    'value' => $info->valor,
                ];
            }
        }

        // Siempre incluir user con sus claves, incluso si están vacías
        $userData = [
            'user' => [
                'header' => $headerText !== '' ? $headerText : null,
                'extras' => !empty($extrasArray) ? $extrasArray : [],
                'footer' => $footerText !== '' ? $footerText : null,
            ]
        ];

        $params = [
            'system' => [
                'logo' => $logo,
            ]
        ];

        // Mezclamos siempre con userData
        $params = array_merge($params, $userData);

        return $report->render($invoice, $params);
    }
}