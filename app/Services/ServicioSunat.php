<?php

namespace App\Services;

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

class ServicioSunat
{
    public static function facturar($ventaId)
    {

        $venta = Venta::find($ventaId);
        $correlativo = null;

        if (!$venta) {
            throw new Exception("La venta proporcionada no existe");
        }
        $negocioId = $venta->negocio_id;
        $negocio = Negocio::find($negocioId);
        $seeOptions = [
            'certificate' => Storage::disk('public')->get($negocio->certificado),
            'production' => $negocio->modo == 'produccion',
            'ruc' => $negocio->ruc,
            'user' => $negocio->usuario_sol,
            'password' => $negocio->clave_sol,
        ];


        $see = self::getSee($seeOptions);
        $invoice = self::getInvoice($venta);
        $result = $see->send($invoice);
    }
    public static function getSee($seeOptions)
    {
        $isProduction = $seeOptions['production'] ?? false;
        $see = new See();
        $see->setCertificate($seeOptions['certificate']);
        $see->setService($isProduction ? SunatEndpoints::FE_PRODUCCION : SunatEndpoints::FE_BETA);
        $see->setClaveSOL($seeOptions['ruc'], $seeOptions['user'], $seeOptions['password']);

        return $see;
    }
    public static function getInvoice($venta)
    {
        $data['ublVersion'] = '2.1';
        $data['tipoOperacion'] = '0101'; // Venta interna
        $data['tipoDoc'] = $venta->tipo_comprobante_codigo;
        $data['serie'] = $venta->serie_comprobante;
        $data['correlativo'] = $venta->correlativo_comprobante;
        $data['fechaEmision'] = $venta->fecha_emision;
        $data['tipoMoneda'] = 'PEN';

        // Company (objeto generado con tu método)
        $data['company'] = self::getCompany([
            'ruc'=>'',
            'razonSocial'=>'',
            'nombreComercial'=>'',
            'address'=>self::getAddress([
                'ubigueo'=>'',
                'departamento'=>'',
                'provincia'=>'',
                'distrito'=>'',
                'urbanizacion'=>'',
                'direccion'=>'',
                'codLocal'=>''
            ]);
        ]);
        
        // Client (estructura mínima esperada por Greenter)
        $data['client'] = $this->getClient([
            'tipoDoc' => $venta->tipo_documento_cliente,
            'numDoc' => $venta->documento_cliente,
            'rznSocial' => $venta->nombre_cliente ?: 'Cliente Genérico',
        ]);

        // Monto de operaciones
        $data['mtoOperGravadas'] = $venta->valor_venta ?? 0;
        $data['mtoOperExoneradas'] = 0;
        $data['mtoOperInafectas'] = 0;
        $data['mtoOperExportacion'] = 0;
        $data['mtoOperGratuitas'] = 0;

        // Impuestos
        $data['mtoIGV'] = $venta->monto_igv ?? 0;
        $data['mtoIGVGratuitas'] = $venta->monto_igv_gratuito ?? 0;
        $data['icbper'] = $venta->icbper ?? 0;
        $data['totalImpuestos'] = ($data['mtoIGV'] ?? 0) + ($data['icbper'] ?? 0);

        // Totales
        $data['valorVenta'] = $venta->valor_venta ?? 0;
        $data['subTotal'] = $venta->sub_total ?? 0;
        $data['redondeo'] = $venta->redondeo ?? 0;
        $data['mtoImpVenta'] = $venta->monto_importe_venta ?? 0;

        // Detalles de productos
        $data['details'] = self::getDetails($detalles);

        // Leyendas (ejemplo: monto en letras)
        $data['legends'] = [
            [
                'code' => '1000',
                'value' => $this->numToWords($venta->monto_importe_venta ?? 0) . ' SOLES'
            ]
        ];

        return (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion($data['tipoOperacion'] ?? null) // Venta - Catalog. 51
            ->setTipoDoc($data['tipoDoc'] ?? null) // Factura - Catalog. 01 
            ->setSerie($data['serie'] ?? null)
            ->setCorrelativo($data['correlativo'] ?? null)
            ->setFechaEmision(new DateTime($data['fechaEmision'] ?? null)) // Zona horaria: Lima
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda($data['tipoMoneda'] ?? null) // Sol - Catalog. 02
            ->setCompany($data['company'])
            ->setClient($this->getClient($data['client']))

            //Mto Operaciones
            ->setMtoOperGravadas($data['mtoOperGravadas'] ?? null)
            ->setMtoOperExoneradas($data['mtoOperExoneradas'] ?? null)
            ->setMtoOperInafectas($data['mtoOperInafectas'] ?? null)
            ->setMtoOperExportacion($data['mtoOperExportacion'] ?? null)
            ->setMtoOperGratuitas($data['mtoOperGratuitas'] ?? null)

            //Impuestos
            ->setMtoIGV($data['mtoIGV'])
            ->setMtoIGVGratuitas($data['mtoIGVGratuitas'])
            ->setIcbper($data['icbper'])
            ->setTotalImpuestos($data['totalImpuestos'])

            //Totales
            ->setValorVenta($data['valorVenta'])
            ->setSubTotal($data['subTotal'])
            ->setRedondeo($data['redondeo'])
            ->setMtoImpVenta($data['mtoImpVenta'])

            //Productos
            ->setDetails($data['details'])

            //Leyendas
            ->setLegends($this->getLegends($data['legends']));
    }
    public static function getCompany($company)
    {
        return (new Company())
            ->setRuc($company['ruc'] ?? null)
            ->setRazonSocial($company['razonSocial'] ?? null)
            ->setNombreComercial($company['nombreComercial'] ?? null)
            ->setAddress($company['address'] ?? null);
    }
    public static function getAddress($address)
    {
        return (new Address())
            ->setUbigueo($address['ubigueo'] ?? null)
            ->setDepartamento($address['departamento'] ?? null)
            ->setProvincia($address['provincia'] ?? null)
            ->setDistrito($address['distrito'] ?? null)
            ->setUrbanizacion($address['urbanizacion'] ?? null)
            ->setDireccion($address['direccion'] ?? null)
            ->setCodLocal($address['codLocal'] ?? null); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

    }
    public static function getDetails($details)
    {
        $green_details = [];

        foreach ($details as $detail) {
            $green_details[] = (new SaleDetail())
                ->setCodProducto($detail['codProducto'] ?? null)
                ->setUnidad($detail['unidad'] ?? null) // Unidad - Catalog. 03
                ->setCantidad($detail['cantidad'] ?? null)
                ->setMtoValorUnitario($detail['mtoValorUnitario'] ?? null)
                ->setDescripcion($detail['descripcion'] ?? null)
                ->setMtoBaseIgv($detail['mtoBaseIgv'] ?? null)
                ->setPorcentajeIgv($detail['porcentajeIgv'] ?? null) // 18%
                ->setIgv($detail['igv'] ?? null)
                ->setFactorIcbper($detail['factorIcbper'] ?? null) // 0.3%
                ->setIcbper($detail['icbper'] ?? null)
                ->setTipAfeIgv($detail['tipAfeIgv'] ?? null) // Gravado Op. Onerosa - Catalog. 07
                ->setTotalImpuestos($detail['totalImpuestos'] ?? null) // Suma de impuestos en el detalle
                ->setMtoValorVenta($detail['mtoValorVenta'] ?? null)
                ->setMtoPrecioUnitario($detail['mtoPrecioUnitario'] ?? null);
        }
        return $green_details;
    }
    private $dataClient = [];
    public function factura($seeOptions)
    {
        if (empty($this->dataClient)) {
            throw new \Exception("Debe configurar el cliente antes de generar la factura (use setClient()).");
        }

        // Client
        $client = (new Client())
            ->setTipoDoc($this->dataClient['tipoDoc'])
            ->setNumDoc($this->dataClient['numDoc'])
            ->setRznSocial($this->dataClient['rznSocial']);

        // Emisor
        $address = (new Address())
            ->setUbigueo('150101')
            ->setDepartamento('LIMA')
            ->setProvincia('LIMA')
            ->setDistrito('LIMA')
            ->setUrbanizacion('-')
            ->setDireccion('Av. Villa Nueva 221')
            ->setCodLocal('0000'); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

        $company = (new Company())
            ->setRuc('20123456789')
            ->setRazonSocial('GREEN SAC')
            ->setNombreComercial('GREEN')
            ->setAddress($address);

        // Venta
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta - Catalog. 51
            ->setTipoDoc('01') // Factura - Catalog. 01 
            ->setSerie('F001')
            ->setCorrelativo('1')
            ->setFechaEmision(new DateTime('2020-08-24 13:05:00-05:00')) // Zona horaria: Lima
            ->setFormaPago(new FormaPagoContado()) // FormaPago: Contado
            ->setTipoMoneda('PEN') // Sol - Catalog. 02
            ->setCompany($company)
            ->setClient(($client))
            ->setMtoOperGravadas(100.00)
            ->setMtoIGV(18.00)
            ->setTotalImpuestos(18.00)
            ->setValorVenta(100.00)
            ->setSubTotal(118.00)
            ->setMtoImpVenta(118.00)
        ;

        $item = (new SaleDetail())
            ->setCodProducto('P001')
            ->setUnidad('NIU') // Unidad - Catalog. 03
            ->setCantidad(2)
            ->setMtoValorUnitario(50.00)
            ->setDescripcion('PRODUCTO 1')
            ->setMtoBaseIgv(100)
            ->setPorcentajeIgv(18.00) // 18%
            ->setIgv(18.00)
            ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
            ->setTotalImpuestos(18.00) // Suma de impuestos en el detalle
            ->setMtoValorVenta(100.00)
            ->setMtoPrecioUnitario(59.00)
        ;

        $legend = (new Legend())
            ->setCode('1000') // Monto en letras - Catalog. 52
            ->setValue('SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES');

        $invoice->setDetails([$item])
            ->setLegends([$legend]);


        ///////////////////CONFIG FILE
        $isProduction = $seeOptions['production'] ?? false;
        $see = new See();
        $see->setCertificate($seeOptions['certificate']);
        $see->setService($isProduction ? SunatEndpoints::FE_PRODUCCION : SunatEndpoints::FE_BETA);
        $see->setClaveSOL($seeOptions['ruc'], $seeOptions['user'], $seeOptions['password']);

        $result = $see->send($invoice);

        // Guardar XML firmado digitalmente.
        file_put_contents(
            $invoice->getName() . '.xml',
            $see->getFactory()->getLastXml()
        );

        // Verificamos que la conexión con SUNAT fue exitosa.
        if (!$result->isSuccess()) {
            // Mostrar error al conectarse a SUNAT.
            echo 'Codigo Error: ' . $result->getError()->getCode();
            echo 'Mensaje Error: ' . $result->getError()->getMessage();
            exit();
        }

        // Guardamos el CDR
        file_put_contents('R-' . $invoice->getName() . '.zip', $result->getCdrZip());



        ///////////////////leemos el CDR

        $cdr = $result->getCdrResponse();

        $code = (int) $cdr->getCode();

        if ($code === 0) {
            echo 'ESTADO: ACEPTADA' . PHP_EOL;
            if (count($cdr->getNotes()) > 0) {
                echo 'OBSERVACIONES:' . PHP_EOL;
                // Corregir estas observaciones en siguientes emisiones.
                var_dump($cdr->getNotes());
            }
        } else if ($code >= 2000 && $code <= 3999) {
            echo 'ESTADO: RECHAZADA' . PHP_EOL;
        } else {
            /* Esto no debería darse, pero si ocurre, es un CDR inválido que debería tratarse como un error-excepción. */
            /*code: 0100 a 1999 */
            echo 'Excepción';
        }

        echo $cdr->getDescription() . PHP_EOL;
    }
    /**
     * Valida y guarda los datos del cliente que serán usados en la factura.
     *
     * @param array $client Datos del cliente:
     *                      - tipoDoc: string (ej. '1' para DNI, '6' para RUC, etc.)
     *                      - numDoc: string (número del documento)
     *                      - rznSocial: string (nombre o razón social)
     *
     * @return self
     *
     * @throws \Exception Si falta algún dato o el tipo de documento no es válido.
     */
    public function setClient(array $client): self
    {
        $tiposPermitidos = ['0', '1', '4', '6', '7', 'A', 'B', 'C', 'D'];

        if (empty($client['tipoDoc']) || !in_array($client['tipoDoc'], $tiposPermitidos)) {
            throw new \Exception("Tipo de documento inválido o no permitido.");
        }

        if (empty($client['numDoc']) || !is_string($client['numDoc'])) {
            throw new \Exception("Número de documento inválido.");
        }

        if (empty($client['rznSocial']) || !is_string($client['rznSocial'])) {
            throw new \Exception("Razón social inválida.");
        }

        $this->dataClient = $client;

        return $this; // para permitir encadenamiento
    }

}