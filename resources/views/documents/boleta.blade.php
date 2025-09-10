<!-- resources/views/pdf/ticket.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Courier', monospace;
            font-size: 8pt;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 5px;
        }

        .header img {
            width: 60px;
            margin-bottom: 5px;
        }

        .header .company-name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .ruc {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .address {
            word-wrap: break-word;
            margin-bottom: 5px;
        }

        .header .location {
            margin-bottom: 5px;
        }

        .title {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
            margin-bottom: 5px
        }

        .title2 {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
            margin-bottom: 5px;
            font-size: 20px;
            font-family: Arial, Helvetica, sans-serif
        }

        .details {
            text-align: left;
            margin-bottom: 5px;
        }

        .details table {
            width: 100%;
        }

        .details th,
        .details td {
            padding: 2px 0;
        }

        .details .line {
            border-bottom: 1px dotted black;
            margin: 5px 0;
        }

        .totals {
            text-align: left;
            margin-bottom: 5px;
        }

        .qr {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 5px;
            font-size: 7pt;
        }

        table {
            width: 100%
        }

        @page {
            margin: 15px;
            /* Establecer márgenes a cero */
        }

        body {
            margin: 0;
            /* Eliminar margen del cuerpo */
        }

        .ticket {
            width: 100%;
            /* Ajustar el ancho al 100% del papel */
            /* Puedes agregar más estilos para personalizar tu boleta */
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="title2">{{ $nombre_legal }}</h1>
        <div class="company-name">{{ $nombre_comercial }}</div>
        <div class="ruc">RUC: {{ $ruc }}</div>
        <div class="address">
            {{ $direccion }}
        </div>
    </div>

    <div class="title">{{ $text_document }}</div>
    <div class="title">{{ $serie_comprobante }}-{{ $correlativo_comprobante }}</div>
    <div class="details">
        <table>
            <tr>
                <td>Fecha de Emisión: {{ $fecha_emision }}</td>
            </tr>
            <tr>
                {{-- Using the correct variable names for client information --}}
                <td>Cliente: {{ $documento_cliente }} - {{ $nombre_cliente }}</td>
            </tr>
        </table>
        <div class="line"></div>
        <table style="width:100%" cellspacing="5">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Descr.</th>
                    <th style="text-align:center">Cant.</th>
                    <th style="text-align:right">P.Unit.</th>
                    <th style="text-align:right">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td valign="TOP">{{ $item['codigo'] }}</td> {{-- Changed from 'code' to 'codigo' --}}
                        <td colspan="4">{{ $item['descripcion'] }}</td> {{-- Changed from 'description' to 'descripcion' --}}
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td style="text-align:center">{{ $item['cantidad'] }}</td> {{-- Changed from 'quantity' to 'cantidad' --}}


                        @if ($tipo_comprobante_codigo === 'ticket')
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($item['monto_precio_unitario'], 2, '.', ',') }}</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($item['monto_precio_unitario'] * $item['cantidad'], 2, '.', ',') }}</td>
                        @else
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($item['monto_valor_unitario'], 2, '.', ',') }}</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($item['monto_valor_venta'], 2, '.', ',') }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                @if ($tipo_comprobante_codigo === 'ticket')
                    {{-- Solo mostrar el total simple --}}
                    <tr>
                        <td colspan="4" style="text-align:right">Total:</td>
                        <td style="text-align:right; white-space: nowrap;">S/
                            {{ number_format($monto_importe_venta, 2, '.', ',') }}
                        </td>
                    </tr>
                @else
                    @if ($monto_operaciones_gravadas > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">Op. Gravada:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_operaciones_gravadas, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($monto_operaciones_exoneradas > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">Op. Exonerada:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_operaciones_exoneradas, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($monto_operaciones_inafectas > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">Op. Inafecta:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_operaciones_inafectas, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($monto_operaciones_exportacion > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">Op. Exportación:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_operaciones_exportacion, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($monto_operaciones_gratuitas > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">Op. Gratuitas:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_operaciones_gratuitas, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($monto_igv > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">IGV:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_igv, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($monto_igv_gratuito > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">IGV Gratuito:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_igv_gratuito, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($icbper > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">ICBPER:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($icbper, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($total_impuestos > 0)
                        <tr>
                            <td colspan="4" style="text-align:right">Total Impuestos:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($total_impuestos, 2, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if ($redondeo != 0)
                        {{-- Display if there's any rounding, positive or negative --}}
                        <tr>
                            <td colspan="4" style="text-align:right">Redondeo:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($redondeo, 2, '.', ',') }}</td>
                        </tr>
                    @endif
                    {{-- Total amount always displayed --}}
                    <tr>
                        <td colspan="4" style="text-align:right">Total:</td>
                        <td style="text-align:right; white-space: nowrap;">S/
                            {{ number_format($monto_importe_venta, 2, '.', ',') }}</td> {{-- Changed from 'total_amount' to 'monto_importe_venta' --}}
                    </tr>

                    <tr>
                        <td colspan="100%">
                            <div class="line"></div>
                        </td>
                    </tr>

                    {{-- Conditional display for payment types --}}
                    @if ($modo_venta === 'credito' && $estado === 'por_pagar')
                        <tr>
                            <td colspan="4" style="text-align:right">Por pagar:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_importe_venta, 2, '.', ',') }}</td>
                        </tr>
                    @elseif ($modo_venta === 'credito' && $estado === 'parcial')
                        <tr>
                            <td colspan="4" style="text-align:right">Primer Pago:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_importe_venta - $sub_total, 2, '.', ',') }}</td>
                            {{-- Assuming sub_total here represents the remaining amount to be paid --}}
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align:right">Saldo a cuenta:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($sub_total, 2, '.', ',') }}</td> {{-- Assuming sub_total here represents the remaining amount to be paid --}}
                        </tr>
                    @else
                        {{-- Assuming 'contado' or any other fully paid mode --}}
                        <tr>
                            <td colspan="4" style="text-align:right">Efectivo Soles:</td>
                            <td style="text-align:right; white-space: nowrap;">S/
                                {{ number_format($monto_importe_venta, 2, '.', ',') }}</td>
                        </tr>
                        @if ($monto_importe_venta - $total_impuestos > 0)
                            {{-- This logic for "vuelto" might need adjustment based on how it's calculated in your system --}}
                            <tr>
                                <td colspan="4" style="text-align:right">Vuelto:</td>
                                <td style="text-align:right; white-space: nowrap;">S/
                                    {{ number_format(0, 2, '.', ',') }}</td> {{-- You need to pass the actual 'vuelto' value from your controller --}}
                            </tr>
                        @endif
                    @endif
                @endif
            </tfoot>
        </table>
        <div class="line"></div>
    </div>

    <div class="qr-code" style="text-align: center; margin-top: 30px; margin-bottom: 30px; display: block;">

        <img style="width: 100px;" src="{{ $qrBase64 }}" alt="Código QR" />

    </div>

    <div class="footer">
        Este es un documento emitido electrónicamente.<br>
        Guarde este documento para cualquier reclamo.<br>
        Gracias por su compra.
    </div>
</body>

</html>
