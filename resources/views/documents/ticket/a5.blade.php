<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta de Venta Electrónica - A5</title>
    <style>
        @page {
            margin: 20px;
            /* Establecer márgenes a cero */
        }

        body {
            margin: 0;
            /* Eliminar margen del cuerpo */
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 7pt;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .receipt-container {
            width: 100%;
            margin: 0 auto;
        }

        /* Header Section - converted from flex to table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

        .header-table td {
            vertical-align: top;
            padding-bottom: 8px;
        }
        .company-logo {
            width: 100%;
        }

        .company-name {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-details {
            font-size: 8pt;
        }

        .receipt-box {
            border: 1px solid #000;
            padding: 2px;
            /* Reduced from 8px */
            text-align: center;
            width: 100%;

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
            font-weight: bold;
            margin-bottom: 2px;
            /* Reduced from 3px */
        }

        .receipt-number {
            font-size: 8pt;
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
            margin-bottom: 5px;
            /* Reduced from 15px */
            font-size: 7pt;
            /* Reduced from 10pt */
        }

        .items-table th {
            background-color: #000;
            color: white;
            padding: 6px 2px;
            /* Reduced from 8px 4px */
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
        }

        .items-table td {
            padding: 1px;
            border: 1px solid #363636ff;
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
            margin:3px;
            /* Reduced from 20px */
            border-top: 1px solid #000;
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
        *{
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Header Section - converted from flex div to table -->
        <table style="width:100%">
            <!-- separamos en dos grandes grupos-->
            <tr>
                <td style="vertical-align: top;">
                    <table style="margin-bottom:8px;">
                        <tr>
                            <td style="vertical-align: top;">
                                <div class="company-logo">
                                    @if ($logoBase64)
                                        <img src="{{ $logoBase64 }}" alt="Logo" style="width: 100px; height: auto;">
                                    @endif
                                </div>
                            </td>
                            <td style="vertical-align: top;">
                                <div class="company-name">{{ $nombre_legal }}</div>
                                <div class="company-details">
                                    <div class="bold">{{ $nombre_comercial }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:245px;vertical-align: top;">
                    <div class="receipt-box">
                        <div class="receipt-title">R.U.C. N° {{ $ruc }}</div>
                        <div class="receipt-title">NOTA DE VENTA</div>
                        <div class="receipt-number">{{ $serie_comprobante }}-{{ $correlativo_comprobante }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">

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
                        <tr>
                            <td>
                                <strong>DIRECCIÓN:</strong>
                            </td>
                            <td>
                                {{ $direccion }}
                            </td>
                        </tr>
                        @if (!empty($informacion['Cabecera']))

                            @foreach ($informacion['Cabecera'] as $item)
                                <tr>
                                    <td>
                                        <strong>{{ mb_strtoupper($item['clave']) }}:</strong>
                                    </td>
                                    <td> {{ $item['valor'] }}</td>
                                </tr>
                            @endforeach

                        @endif
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table>
                        <tr>
                            <td>
                                <strong>FECHA EMISIÓN:</strong>
                            </td>
                            <td>
                                {{ $fecha_emision }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>MONEDA:</strong>
                            </td>
                            <td>
                                SOLES
                            </td>
                        </tr>
                    </table>
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
                        <td class="text-right">
                            {{ number_format($item['monto_precio_unitario'] * $item['cantidad'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-text">{{ $texto_total_letras }}</div>
        </div>

        <!-- Footer Section - converted from flex div to table -->
        <table class="footer-table">
            <tr>
                <td class="payment-info" style="vertical-align: top;">
                    <div><strong>CONDICIÓN DE PAGO:</strong> {{ $modalidad_pago }}</div>
                    @if (!empty($informacion['Pie']))
                        <div class="footer-extra">
                            @foreach ($informacion['Pie'] as $item)
                                <div><strong>{{ mb_strtoupper($item['clave']) }}:</strong> {{ $item['valor'] }}</div>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td style="text-align:right; vertical-align: top;">
                    <div  style="border-top: 1px solid #000;">
                        TOTAL S/. {{ number_format($monto_importe_venta, 2) }}
                    </div>
                </td>
                <!--<td class="qr-section" style="text-align:right;">
                    <img src="{{ $qrBase64 }}" alt="Código QR" style="width: 70px; height: 70px;">
                </td>-->
            </tr>
        </table>
    </div>
</body>

</html>