<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta de Venta Electrónica - A5</title>
    <style>
        @page {
            margin: 15px;
            /* Establecer márgenes a cero */
        }

        body {
            margin: 0;
            /* Eliminar margen del cuerpo */
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            /* Reduced from 11pt for A5 format */
            line-height: 1.2;
            /* Reduced from 1.3 for better spacing */
            margin: 0;
            padding: 0;
            color: #000;
        }

        .receipt-container {
            width: 100%;
            max-width: 148mm;
            /* A5 width minus margins */
            margin: 0 auto;
        }

        /* Header Section - converted from flex to table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            /* Reduced from 15px */
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            /* Reduced from 10px */
        }

        .header-table td {
            vertical-align: top;
            padding-bottom: 8px;
        }

        .company-info {
            width: 70%;
        }

        .company-logo {
            width: 100%;
            /* Reduced from 80px */
            height: 70px;
            /* Reduced from 80px */
            margin-bottom: 6px;
            /* Reduced from 8px */
        }

        .company-name {
            font-size: 11pt;
            /* Reduced from 16pt */
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            /* Reduced from 3px */
        }

        .company-details {
            font-size: 8pt;
            /* Reduced from 9pt */
            line-height: 1.1;
            /* Reduced from 1.2 */
        }

        .receipt-box {
            border: 2px solid #000;
            padding: 2px;
            /* Reduced from 8px */
            text-align: center;
            width: 170px;
            /* Reduced from 120px */
            margin-bottom: 8px;
            ;
        }

        .ruc-number {
            font-size: 9pt;
            /* Reduced from 10pt */
            font-weight: bold;
            margin-bottom: 2px;
            /* Reduced from 3px */
        }

        .receipt-title {
            font-size: 9pt;
            /* Reduced from 12pt */
            font-weight: bold;
            margin-bottom: 2px;
            /* Reduced from 3px */
        }

        .receipt-number {
            font-size: 8pt;
            /* Reduced from 11pt */
            font-weight: bold;
        }

        /* Document Details - converted from flex to table */
        .document-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            /* Reduced from 15px */
            font-size: 8pt;
            /* Reduced from 10pt */
        }

        .document-details-table td {
            vertical-align: top;
            width: 50%;
        }

        .right-details {
            text-align: right;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            /* Reduced from 15px */
            font-size: 8pt;
            /* Reduced from 10pt */
        }

        .items-table th {
            background-color: #000;
            color: white;
            padding: 6px 3px;
            /* Reduced from 8px 4px */
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
        }

        .items-table td {
            padding: 4px 3px;
            /* Reduced from 6px 4px */
            border: 1px solid #000;
            vertical-align: top;
        }

        .items-table .text-center {
            text-align: center;
        }

        .items-table .text-right {
            text-align: right;
        }

        /* Totals Section */
        .totals-section {
            margin-top: 15px;
            /* Reduced from 20px */
            border-top: 1px solid #000;
            padding-top: 8px;
            /* Reduced from 10px */
        }

        .totals-text {
            text-align: center;
            font-size: 8pt;
            /* Reduced from 10pt */
            margin-bottom: 8px;
            /* Reduced from 10px */
        }

        .totals-table {
            width: 100%;
            font-size: 9pt;
            /* Reduced from 11pt */
        }

        .totals-table td {
            padding: 2px 0;
            /* Reduced from 3px 0 */
        }

        .totals-table .label {
            text-align: right;
            font-weight: bold;
            padding-right: 15px;
            /* Reduced from 20px */
        }

        .totals-table .amount {
            text-align: right;
            font-weight: bold;
            width: 70px;
            /* Reduced from 80px */
        }

        /* Footer Section - converted from flex to table */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            /* Reduced from 20px */
        }

        .footer-table td {
            vertical-align: bottom;
        }

        .payment-info {
            font-size: 7pt;
            /* Reduced from 9pt */
            line-height: 1.1;
            /* Reduced from 1.2 */
            width: 70%;
        }

        .qr-section {
            text-align: center;
            width: 30%;
        }

        .qr-code {
            width: 70px;
            /* Reduced from 80px */
            height: 70px;
            /* Reduced from 80px */
            border: 1px solid #000;
            margin-bottom: 4px;
            /* Reduced from 5px */
        }

        .hash-code {
            font-size: 6pt;
            /* Reduced from 7pt */
            word-break: break-all;
            max-width: 70px;
            /* Reduced from 80px */
        }

        /* Utility Classes */
        .bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-5 {
            margin-bottom: 4px;
            /* Reduced from 5px */
        }

        .mb-10 {
            margin-bottom: 8px;
            /* Reduced from 10px */
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Header Section - converted from flex div to table -->
        <table style="width:100%">
            <!-- separamos en dos grandes grupos-->
            <tr>
                <td style="width:66.6%;" vertical-align="top">
                    <table>
                        <tr>
                            <td>
                                <div class="company-logo">
                                    @if($logoBase64)
                                        <img src="{{ $logoBase64 }}" alt="Logo" style="width: 80px; height: auto;">
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="company-name">{{ $nombre_legal }}</div>
                                <div class="company-details">
                                    <div class="bold">{{ $nombre_comercial }}</div>
                                    <div>{{ $ruc }}</div>
                                    <div>{{ $direccion }}</div>
                                    @if(!empty($informacion['Cabecera']))
                                        <div class="header-section">
                                            @foreach($informacion['Cabecera'] as $item)
                                                <div class="uppercase">{{ $item['clave'] }}: {{ $item['valor'] }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <strong>DOCUMENTO:</strong>
                            </td>
                            <td>
                                {{ $documento_cliente }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>CLIENTE:</strong>
                            </td>
                            <td>
                                {{ $nombre_cliente }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:33.4%;" vertical-align="top">
                    <div class="receipt-box">
                        <div class="receipt-title">NOTA DE VENTA</div>
                        <div class="receipt-number">{{ $serie_comprobante }}-{{ $correlativo_comprobante }}</div>
                    </div>
                    <div><strong>FECHA EMISIÓN:</strong> {{ $fecha_emision }}</div>
                    <div><strong>MONEDA:</strong> SOLES</div>
                </td>
            </tr>
        </table>
        

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">N°</th>
                    <th style="width: 13%;">UNIDAD</th>
                    <th style="width: 16%;">CÓDIGO</th>
                    <th style="width: 32%;">DESCRIPCIÓN</th>
                    <th style="width: 8%;">CANT.</th>
                    <th style="width: 10%;">P. UNIT.</th>
                    <th style="width: 10%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item['unidad'] }}</td>
                        <td class="text-center">{{ $item['codigo'] }}</td>
                        <td>{{ $item['descripcion'] }}</td>
                        <td class="text-center">{{ number_format($item['cantidad'], 2) }}</td>
                        <td class="text-right">{{ number_format($item['monto_precio_unitario'], 2) }}</td>
                        <td class="text-right">{{ number_format($item['monto_precio_unitario'] * $item['cantidad'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-text">{{ $texto_total_letras }}</div>

            <table class="totals-table">
                <tr style="border-top: 1px solid #000;">
                    <td class="label">TOTAL</td>
                    <td class="amount">S/</td>
                    <td class="amount">{{ number_format($monto_importe_venta, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer Section - converted from flex div to table -->
        <table class="footer-table">
            <tr>
                <td class="payment-info">
                    <div><strong>CONDICIÓN DE PAGO:</strong> {{ $modalidad_pago }}</div>
                    @if(!empty($informacion['Pie']))
                        <div class="footer-extra">
                            @foreach($informacion['Pie'] as $item)
                                <div><strong>{{ mb_strtoupper($item['clave']) }}:</strong> {{ $item['valor'] }}</div>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td class="qr-section" style="text-align:right;">
                    <img src="{{ $qrBase64 }}" alt="Código QR" style="width: 70px; height: 70px;">
                </td>
            </tr>
        </table>
    </div>
</body>

</html>