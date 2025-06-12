<?php

namespace App\Services;

use App\Models\Negocio;
use App\Models\Nota;
use App\Models\SucursalCorrelativo;
use App\Models\Venta;
use App\Services\Facturacion\PdfGenerators\A4VoucherGenerator;
use Exception;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Luecano\NumeroALetras\NumeroALetras;
use Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

class ComprobanteServicio
{
    protected SunatService $sunatService;
    protected $catalogoComprobantes = [
        'factura' => '01',
        'boleta' => '03',
        'nota_credito' => '07',
        'nota_debito' => '08',
        'guia_remision' => '09',
        'ticket' => '12',
        'banco' => '13',
        'afp' => '18',
        'guia_transp' => '31',
        'seae' => '56',
        'guia_remision_comp' => '71',
        'guia_transp_comp' => '72',
        'retencion' => '20',
        'percepcion' => '40',
        'percepcion_venta_interna' => '41',
    ];
    protected array $catalogoTipoDocumentoCliente = [
        '0' => 'Doc. trib. no dom. sin RUC',
        '1' => 'Doc. Nacional de identidad',
        '4' => 'Carnet de extranjería',
        '6' => 'Registro Único de contribuyentes',
        '7' => 'Pasaporte',
        'A' => 'Cédula Diplomática de identidad',
        'B' => 'Doc. identidad país residencia - no domiciliado',
        'C' => 'Tax Identificación Number (TIN) – Doc Trib PP.NN',
        'D' => 'Identification Number (IN) – Doc Trib PP. JJ',
        'E' => 'TAM - Tarjeta Andina de Migración',
        'F' => 'Permiso Temporal de Permanencia (PTP)',
    ];

    public function __construct()
    {
        $this->sunatService = new SunatService();
    }

    /**
     * Método principal que genera el comprobante para una venta.
     *
     * @param int $ventaId
     * @return mixed
     * @throws Exception
     */
    public function generar(int $ventaId)
    {
        $venta = $this->obtenerVenta($ventaId);

        //$voucherPdf = VoucherServicio::generarDocumento($venta);
        $opciones = $this->prepararOpcionesSunat($venta->negocio);
        $see = $this->obtenerServicioSunat($opciones);
        $tipoDoc = $venta->tipo_comprobante_codigo;
        $numeracion = $this->obtenerNumeracion($venta->sucursal_id, $tipoDoc);

         $data = [
            'negocio' => $venta->negocio,
            'fecha_emision' => $venta->fecha_emision,
            'tipo_documento' => $tipoDoc,
            'venta' => $venta
        ];

        $invoice = $this->prepararDatosInvoice($data, $numeracion);

        $result = $see->send($invoice);

        if (!$result->isSuccess()) {
            $error = $result->getError();
            throw new Exception("SUNAT Error {$error->getCode()}: {$error->getMessage()}");
        }

        // Procesar respuesta de SUNAT y guardar XML y CDR
        $sunatResponse = $this->procesarRespuestaSunat($invoice, $result, $see);

        $sunatComprobantePdf = A4VoucherGenerator::generarDocumento($invoice, $venta);
        $voucherPdf = VoucherServicio::generarDocumento($venta);

        // Actualizar venta
        $venta->sunat_comprobante_pdf = $sunatComprobantePdf;
        $venta->voucher_pdf = $voucherPdf;
        $venta->sunat_xml_firmado = $sunatResponse['xml_path'];
        $venta->sunat_cdr = $sunatResponse['cdr_path'];
        $venta->serie_comprobante = $numeracion['serie'];
        $venta->correlativo_comprobante = $numeracion['correlativo'];
        $venta->save();

        // Actualizar correlativo
        $correlativo = $numeracion['modelo'];
        $correlativo->correlativo_actual = $numeracion['correlativo'];
        $correlativo->save();
    }
    public function generarNota($notaId)
    {
        $nota = $this->obtenerNota($notaId);

        //$voucherPdf = VoucherServicio::generarDocumento($venta);
        $opciones = $this->prepararOpcionesSunat($nota->negocio);
        
        $see = $this->obtenerServicioSunat($opciones);
        $tipoDoc = $nota->tipo_doc;
        $numeracion = $this->obtenerNumeracion($nota->sucursal_id, $tipoDoc);

        $data = [
            'negocio' => $nota->negocio,
            'fecha_emision' => $nota->fecha_emision,
            'tipo_documento' => $tipoDoc,
            'venta_afectada' => $nota->venta,
            'nota' => $nota,
        ];
        $invoice = $this->prepararDatosInvoice($data, $numeracion);
        
        $result = $see->send($invoice);

        if (!$result->isSuccess()) {
            $error = $result->getError();
            throw new Exception("SUNAT Error {$error->getCode()}: {$error->getMessage()}");
        }
        
        // Procesar respuesta de SUNAT y guardar XML y CDR
        $sunatResponse = $this->procesarRespuestaSunat($invoice, $result, $see);
        
        $sunatComprobantePdf = A4VoucherGenerator::generarDocumento($invoice, $nota->negocio);
        $voucherPdf = null;//VoucherServicio::generarDocumento($venta);

        // Actualizar venta
        $nota->sunat_comprobante_pdf = $sunatComprobantePdf;
        $nota->voucher_pdf = $voucherPdf;
        $nota->sunat_xml_firmado = $sunatResponse['xml_path'];
        $nota->sunat_cdr = $sunatResponse['cdr_path'];
        $nota->serie_comprobante = $numeracion['serie'];
        $nota->correlativo_comprobante = $numeracion['correlativo'];
        $nota->save();

        // Actualizar correlativo
        $correlativo = $numeracion['modelo'];
        $correlativo->correlativo_actual = $numeracion['correlativo'];
        $correlativo->save();
    }
    private function procesarRespuestaSunat($invoice, $result, $see)
    {
        $response = [];
        $response['success'] = $result->isSuccess();

        if (!$response['success']) {
            $response['error'] = [
                'code' => $result->getError()->getCode(),
                'message' => $result->getError()->getMessage()
            ];
            return $response;
        }

        // Obtener XML firmado directamente del factory
        $signedXml = $see->getFactory()->getLastXml();

        // Obtener CDR (ZIP base64)
        $cdrZip = $result->getCdrZip();

        $cdr = $result->getCdrResponse();
        $response['cdrResponse'] = [
            'code' => (int) $cdr->getCode(),
            'description' => $cdr->getDescription(),
            'notes' => $cdr->getNotes(),
        ];

        // Guardar archivos
        $nowPath = Carbon::now()->format('Y/m');
        $invoiceName = $invoice->getName();

        $signedXmlPath = "$nowPath/{$invoiceName}.xml";
        $cdrZipPath = "$nowPath/{$invoiceName}.zip";

        Storage::disk('public')->put($signedXmlPath, $signedXml);
        Storage::disk('public')->put($cdrZipPath, $cdrZip); // No necesita decode

        $response['xml_path'] = $signedXmlPath;
        $response['cdr_path'] = $cdrZipPath;

        return $response;
    }




    /**
     * Prepara las opciones para el servicio Sunat a partir del negocio.
     *
     * @param Negocio $negocio
     * @return array
     */
    protected function prepararOpcionesSunat(Negocio $negocio): array
    {
        if (!$negocio->certificado) {
            throw new Exception("El negocio no tiene certificado configurado.");
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($negocio->certificado)) {
            throw new Exception("El archivo de certificado no existe en el disco público: {$negocio->certificado}");
        }

        return [
            'certificate' => $disk->get($negocio->certificado),
            'production' => $negocio->modo === 'produccion',
            'ruc' => $negocio->ruc,
            'user' => $negocio->usuario_sol,
            'password' => $negocio->clave_sol,
        ];
    }

    /**
     * Obtiene la venta o lanza excepción si no existe.
     *
     * @param int $ventaId
     * @throws Exception
     */
    protected function obtenerVenta(int $ventaId)
    {
        $venta = Venta::find($ventaId);
        if (!$venta) {
            throw new Exception("La venta proporcionada no existe");
        }
        return $venta;
    }
    protected function obtenerNota(int $notaId)
    {
        $nota = Nota::find($notaId);
        if (!$nota) {
            throw new Exception("La nota proporcionada no existe");
        }
        return $nota;
    }
    protected function obtenerTipoDoc($tipoComprobante)
    {
        $tipoDoc = $this->catalogoComprobantes[$tipoComprobante] ?? null;

        if (!$tipoDoc) {
            throw new Exception("Tipo de comprobante desconocido.");
        }
        return $tipoDoc;
    }
    protected function obtenerNumeracion($sucursalId, $tipoDoc)
    {
        $correlativos = SucursalCorrelativo::with('correlativo')
            ->where('sucursal_id', $sucursalId)
            ->whereHas('correlativo', function ($q) use ($tipoDoc) {
                $q->where('tipo_comprobante_codigo', $tipoDoc);
            })
            ->get();

        if ($correlativos->count() === 0) {
            throw new Exception("No se encontró configuración de serie y correlativo para el tipo de comprobante [$tipoDoc] en la sucursal [$sucursalId].");
        }

        if ($correlativos->count() > 1) {
            throw new Exception("Existe más de una configuración de correlativo para el tipo de comprobante [$tipoDoc] en la sucursal [$sucursalId]. Debe haber solo una.");
        }

        $correlativo = $correlativos->first()->correlativo;

        if (!$correlativo) {
            throw new Exception("No se pudo obtener el correlativo asociado.");
        }

        return [
            'serie' => $correlativo->serie,
            'correlativo' => $correlativo->correlativo_actual + 1,
            'modelo' => $correlativo,
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | Cálculo de Montos por Tipo de Operación (SUNAT - Catálogo 07)
    |--------------------------------------------------------------------------
    | Este bloque se encarga de clasificar y sumar los valores de los ítems
    | de una factura electrónica según su tipo de afectación al IGV (Impuesto 
    | General a las Ventas), de acuerdo al Catálogo N° 07 de SUNAT.
    | 
    | Se procesan los productos vendidos según su `tipAfeIgv` (tipo de afectación),
    | y se acumulan los montos correspondientes en los siguientes campos globales:
    |
    | - mtoOperGravadas     → Suma de ítems gravados (IGV 18%)
    | - mtoOperExoneradas   → Suma de ítems exonerados (IGV 0%)
    | - mtoOperInafectas    → Suma de ítems inafectos (IGV 0%)
    | - mtoOperGratuitas    → Suma de ítems entregados gratuitamente
    | - mtoIGV              → IGV total de ítems gravados
    | - mtoIGVGratuitas     → IGV correspondiente a productos gratuitos (si aplica)
    | - totalImpuestos      → Suma de todos los impuestos aplicables (IGV + ISC + Otros)
    | - valorVenta          → Suma del valor de venta (sin impuestos) de todos los ítems
    | - subTotal            → valorVenta + totalImpuestos
    | - mtoImpVenta         → Monto total que paga el cliente (subtotal con impuestos)
    |
    | ▸ TIPOS DE AFECTACIÓN AL IGV (Catálogo 07)
    | -----------------------------------------------------
    | Código | Descripción                    | Aplica IGV |
    |--------|--------------------------------|------------|
    | 10     | Gravado - Operación Onerosa    | Sí         |
    | 20     | Exonerado                      | No         |
    | 30     | Inafecto                       | No         |
    | 13     | Gravado - Retiro Gratuito      | Sí         |
    | 32     | Inafecto - Retiro Gratuito     | No         |
    |
    | ▸ REGLAS DE CÁLCULO
    | -----------------------------------------------------
    | 1. Si `tipAfeIgv = 10`: 
    |    - El producto es gravado.
    |    - Se suma su valor a `mtoOperGravadas`.
    |    - Se calcula IGV (porcentajeIgv > 0).
    |
    | 2. Si `tipAfeIgv = 20`: 
    |    - Producto exonerado.
    |    - Se suma a `mtoOperExoneradas`.
    |    - No se aplica IGV.
    |
    | 3. Si `tipAfeIgv = 30`: 
    |    - Producto inafecto.
    |    - Se suma a `mtoOperInafectas`.
    |    - No se aplica IGV.
    |
    | 4. Si `MtoValorGratuito > 0`: 
    |    - Producto entregado gratuitamente.
    |    - Se suma a `mtoOperGratuitas`.
    |    - Si `tipAfeIgv = 13`, también se debe calcular el IGV sobre la base gratuita.
    |
    | 5. Los valores base de IGV (`mtoBaseIgv`) se suman a los campos de operación solo
    |    si su `tipAfeIgv` corresponde a ese grupo (por ejemplo, 10 → gravadas).
    |
    | ▸ VALIDACIONES OBLIGATORIAS
    | -----------------------------------------------------
    | - Si `tipAfeIgv = 10` o `13`, debe calcularse IGV > 0.
    | - Si `MtoValorGratuito > 0` y el tipo es gravado (`13`), se debe calcular `mtoIGVGratuitas`.
    | - La suma de todos los valores de venta (sin impuestos) debe coincidir con `valorVenta`.
    | - La suma total (`mtoImpVenta`) debe ser igual a: `valorVenta + totalImpuestos`.
    |
    | ▸ EJEMPLO RESUMIDO DEL CÁLCULO
    | -----------------------------------------------------
    | item1 (gravado):      200 base, 36 IGV      → mtoOperGravadas += 200
    | item2 (exonerado):    100 base              → mtoOperExoneradas += 100
    | item3 (inafecto):     200 base              → mtoOperInafectas += 200
    | item4 (gratuito - 13):100 gratuito, 18 IGV  → mtoOperGratuitas += 100, mtoIGVGratuitas += 18
    | item5 (gratuito - 32):100 gratuito          → mtoOperGratuitas += 100
    |
    | Totales esperados:
    | mtoOperGravadas     = 200
    | mtoOperExoneradas   = 100
    | mtoOperInafectas    = 200
    | mtoOperGratuitas    = 200
    | mtoIGV              = 36 (item1) + 18 (item4) = 54
    | mtoIGVGratuitas     = 18
    | totalImpuestos      = 36 (solo se declara lo que corresponde al pago)
    | valorVenta          = 500
    | subTotal            = 536
    | mtoImpVenta         = 536
    */
    /*
    protected function prepararDatosInvoice(Venta $venta, $tipoDoc, $fecha, $numeracion)
    {
        $negocio = $venta->negocio;
        $fechaEmision = $this->formatearFechaEmision($fecha);

        $legends = [
            [
                'code' => '1000',
                'value' => $this->montoEnLetras($venta->monto_importe_venta ?? 0),
            ],
        ];

        // Si hay productos gratuitos, agregar leyenda 1002
        if (($venta->monto_operaciones_gratuitas ?? 0) > 0) {
            $legends[] = [
                'code' => '1002',
                'value' => 'TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE',
            ];
        }

        $data = [];

        $data['ublVersion'] = '2.1';
        $data['tipoOperacion'] = '0101';
        $data['tipoDoc'] = $tipoDoc;
        $data['serie'] = $numeracion['serie'];
        $data['correlativo'] = $numeracion['correlativo'];
        $data['fechaEmision'] = $fechaEmision;
        $data['tipoMoneda'] = 'PEN';

        // Empresa emisora
        $data['company'] = [
            'ruc' => $negocio->ruc,
            'razonSocial' => $negocio->nombre_legal,
            'nombreComercial' => $negocio->nombre_comercial,
            'address' => [
                'direccion' => $negocio->direccion,
                'ubigueo' => $negocio->ubigeo,
                'departamento' => $negocio->departamento,
                'provincia' => $negocio->provincia,
                'distrito' => $negocio->distrito,
                'urbanizacion' => $negocio->urbanizacion,
                'codLocal' => $negocio->codigo_pais,
            ]
        ];

        // Cliente
        $data['client'] = [
            'tipoDoc' => $this->validarTipoDocumentoCliente($venta->tipo_documento_cliente),
            'numDoc' => $venta->documento_cliente,
            'rznSocial' => $venta->nombre_cliente,
        ];

        // Montos
        $data['mtoOperGravadas'] = (float) $venta->monto_operaciones_gravadas;
        $data['mtoOperExoneradas'] = (float) $venta->monto_operaciones_exoneradas;
        $data['mtoOperInafectas'] = (float) $venta->monto_operaciones_inafectas;
        $data['mtoOperGratuitas'] = (float) $venta->monto_operaciones_gratuitas;
        $data['mtoIGV'] = (float) $venta->monto_igv;
        $data['mtoIGVGratuitas'] = (float) $venta->monto_igv_gratuito;
        $data['icbper'] = (float) $venta->icbper !== 0.00 ? (float) $venta->icbper : null;
        $data['totalImpuestos'] = (float) $venta->total_impuestos;
        $data['valorVenta'] = (float) $venta->valor_venta;
        $data['subTotal'] = (float) $venta->sub_total;
        $data['mtoImpVenta'] = (float) $venta->monto_importe_venta;

        if ((float) $venta->redondeo !== 0.0) {
            $data['redondeo'] = (float) $venta->redondeo;
        }
        // Detalles
        $data['details'] = $this->getDetallesProductos($venta);

        // Leyendas
        $data['legends'] = $legends;
        //dd($data);
        return $this->sunatService->getInvoice($data);
    }
     */
    protected function prepararDatosInvoice($data, $numeracion)
    {
        if (!isset($data['tipo_documento'])) {
            throw new Exception('Necesita indicar el tipo de documento');
        }
        if (!isset($data['negocio'])) {
            throw new Exception('Necesita indicar el parametro negocio');
        }
        if (!isset($data['fecha_emision'])) {
            throw new Exception('Necesita indicar la fecha de emisión');
        }
        if (in_array($data['tipo_documento'], haystack: ['07', '08']) && !isset($data['nota'])) {
            throw new Exception('Necesita indicar los valores de la nota de credito o debito');
        }


        $negocio = $data['negocio'];
        $fecha = $data['fecha_emision'];
        $venta = $data['venta'] ?? null;
        $nota = $data['nota'] ?? null;
        $ventaAfectada = $data['venta_afectada'] ?? null;

        $tipoDoc = $data['tipo_documento'];

        $fechaEmision = $this->formatearFechaEmision($fecha);

        if (in_array($tipoDoc, ['01', '03'])) { //Factura y Boleta
            $legends = [
                [
                    'code' => '1000',
                    'value' => $this->montoEnLetras($venta->monto_importe_venta ?? 0),
                ],
            ];

            // Si hay productos gratuitos, agregar leyenda 1002
            if (($venta->monto_operaciones_gratuitas ?? 0) > 0) {
                $legends[] = [
                    'code' => '1002',
                    'value' => 'TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE',
                ];
            }
        }


        $data = [];

        $data['ublVersion'] = '2.1';
        $data['tipoOperacion'] = '0101';
        $data['tipoDoc'] = $tipoDoc;
        $data['serie'] = $numeracion['serie'];
        $data['correlativo'] = $numeracion['correlativo'];
        $data['fechaEmision'] = $fechaEmision;
        $data['tipoMoneda'] = 'PEN';

        // Empresa emisora
        $data['company'] = [
            'ruc' => $negocio->ruc,
            'razonSocial' => $negocio->nombre_legal,
            'nombreComercial' => $negocio->nombre_comercial,
            'address' => [
                'direccion' => $negocio->direccion,
                'ubigueo' => $negocio->ubigeo,
                'departamento' => $negocio->departamento,
                'provincia' => $negocio->provincia,
                'distrito' => $negocio->distrito,
                'urbanizacion' => $negocio->urbanizacion,
                'codLocal' => $negocio->codigo_pais,
            ]
        ];

        // Cliente
        if (in_array($tipoDoc, ['01', '03'])) { //Factura y Boleta
            $data['client'] = [
                'tipoDoc' => $this->validarTipoDocumentoCliente($venta->tipo_documento_cliente),
                'numDoc' => $venta->documento_cliente,
                'rznSocial' => $venta->nombre_cliente,
            ];
        }
        if (in_array($tipoDoc, ['07', '08'])) { //Factura y Boleta

            $data['tipDocAfectado'] = $nota->tip_doc_afectado;
            $data['numDocfectado'] = $nota->num_doc_afectado;
            $data['codMotivo'] = $nota->cod_motivo;
            $data['desMotivo'] = $nota->des_motivo;
            $data['client'] = [
                'tipoDoc' => $this->validarTipoDocumentoCliente($ventaAfectada->tipo_documento_cliente),
                'numDoc' => $ventaAfectada->documento_cliente,
                'rznSocial' => $ventaAfectada->nombre_cliente,
            ];
        }


        // Montos
        if (in_array($tipoDoc, ['01', '03'])) { //Factura y Boleta
            $data['mtoOperGravadas'] = (float) $venta->monto_operaciones_gravadas;
            $data['mtoOperExoneradas'] = (float) $venta->monto_operaciones_exoneradas;
            $data['mtoOperInafectas'] = (float) $venta->monto_operaciones_inafectas;
            $data['mtoOperGratuitas'] = (float) $venta->monto_operaciones_gratuitas;
            $data['mtoIGV'] = (float) $venta->monto_igv;
            $data['mtoIGVGratuitas'] = (float) $venta->monto_igv_gratuito;
            $data['icbper'] = (float) $venta->icbper !== 0.00 ? (float) $venta->icbper : null;
            $data['totalImpuestos'] = (float) $venta->total_impuestos;
            $data['valorVenta'] = (float) $venta->valor_venta;
            $data['subTotal'] = (float) $venta->sub_total;
            $data['mtoImpVenta'] = (float) $venta->monto_importe_venta;
            if ((float) $venta->redondeo !== 0.0) {
                $data['redondeo'] = (float) $venta->redondeo;
            }
            // Detalles
            $data['details'] = $this->getDetallesProductos($venta);

            // Leyendas
            $data['legends'] = $legends;
        }
        if (in_array($tipoDoc, ['07', '08'])) { //Factura y Boleta
            //caso anulacion, se mantienen los mismo montos
            $data['mtoOperGravadas'] = (float) $ventaAfectada->monto_operaciones_gravadas;
            $data['mtoOperExoneradas'] = (float) $ventaAfectada->monto_operaciones_exoneradas;
            $data['mtoOperInafectas'] = (float) $ventaAfectada->monto_operaciones_inafectas;
            $data['mtoOperGratuitas'] = (float) $ventaAfectada->monto_operaciones_gratuitas;
            $data['mtoIGV'] = (float) $ventaAfectada->monto_igv;
            $data['mtoIGVGratuitas'] = (float) $ventaAfectada->monto_igv_gratuito;
            $data['icbper'] = $ventaAfectada->icbper ?: null;
            $data['totalImpuestos'] = (float) $ventaAfectada->total_impuestos;
            $data['valorVenta'] = (float) $ventaAfectada->valor_venta;
            $data['subTotal'] = (float) $ventaAfectada->sub_total;
            $data['mtoImpVenta'] = (float) $ventaAfectada->monto_importe_venta;
            if ((float) $ventaAfectada->redondeo !== 0.0) {
                $data['redondeo'] = (float) $ventaAfectada->redondeo;
            }
            // Detalles
            $data['details'] = $this->getDetallesProductos($ventaAfectada);

        }

        if (in_array($tipoDoc, ['01', '03'])) { //Factura y Boleta
            return $this->sunatService->getInvoice($data);
        }
        if (in_array($tipoDoc, ['07', '08'])) { //Factura y Boleta
            return $this->sunatService->getNote($data);
        }
        return null;
    }
    function formatearFechaEmision($fecha): string
    {
        if (!$fecha) {
            throw new InvalidArgumentException('La fecha es nula o vacía.');
        }

        try {
            // Asegurarse que $fecha sea una instancia válida de Carbon
            $carbon = $fecha instanceof Carbon
                ? $fecha
                : Carbon::parse($fecha);

            // Convertir a UTC y devolver en formato ISO 8601 con microsegundos
            return $carbon->setTimezone('UTC')->format('Y-m-d\TH:i:s.u\Z');
        } catch (Exception $e) {
            throw new InvalidArgumentException('La fecha proporcionada no es válida: ' . $e->getMessage());
        }
    }

    protected function montoEnLetras($monto)
    {
        $formatter = new NumeroALetras();
        return $formatter->toInvoice($monto, 2, 'SOLES');
    }
    /**
     * Genera un código de producto con formato P00000{N}
     *
     * @param int $id ID del producto
     * @return string Código de producto formateado
     */
    function generarCodigoProducto($id = null): string
    {
        if ($id === null) {
            return 'N' . str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        return 'P' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }


    protected function getDetallesProductos(Venta $venta): array
    {
        return $venta->detalles->map(function ($detalle) {
            return [
                'codProducto' => $this->generarCodigoProducto($detalle->producto_id),
                'unidad' => $detalle->unidad,
                'descripcion' => $detalle->descripcion,
                'cantidad' => (int) $detalle->cantidad,

                'mtoValorUnitario' => (float) $detalle->monto_valor_unitario,
                'mtoValorGratuito' => (float) $detalle->monto_valor_gratuito,
                'mtoValorVenta' => (float) $detalle->monto_valor_venta,
                'mtoBaseIgv' => (float) $detalle->monto_base_igv,
                'porcentajeIgv' => (float) $detalle->porcentaje_igv,
                'igv' => (float) $detalle->igv,
                'tipAfeIgv' => $detalle->tipo_afectacion_igv,
                'totalImpuestos' => (float) $detalle->total_impuestos,
                'mtoPrecioUnitario' => (float) $detalle->monto_precio_unitario
            ];
        })->toArray();
    }



    /**
     * Obtiene la instancia See configurada con los datos para el servicio Sunat.
     *
     * @param array $options
     */
    protected function obtenerServicioSunat(array $options)
    {
        return $this->sunatService->getSee(
            $options['certificate'],
            $options['ruc'],
            $options['user'],
            $options['password'],
            $options['production']
        );
    }
    #region Validaciones
    protected function validarTipoDocumentoCliente($tipoDoc)
    {
        if (!$tipoDoc) {
            return null;
        }

        if (!array_key_exists($tipoDoc, $this->catalogoTipoDocumentoCliente)) {
            throw new Exception("Tipo de documento del cliente inválido: {$tipoDoc}");
        }

        return $tipoDoc;
    }
    #endregion
}
