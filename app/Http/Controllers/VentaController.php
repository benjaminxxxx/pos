<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\VentaMetodoPago;
use App\Services\ComprobanteService;
use Exception;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class VentaController extends Controller
{
    public function registrar(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre_cliente' => 'nullable|string|max:255',
            'documento_cliente' => 'nullable|string|max:50',
            'tipo_documento_cliente' => 'nullable|string|max:50',
            'subtotal' => 'required|numeric',
            'igv' => 'required|numeric',
            'total' => 'required|numeric',
            'total_pagado' => 'required|numeric',

            'tipo_comprobante_codigo' => 'nullable|string',
            'serie_comprobante' => 'nullable|string|max:10',
            'correlativo_comprobante' => 'nullable|string|max:10',

            'caja_id' => 'nullable|exists:cajas,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'fecha_emision' => 'required|date',
            'fecha_pago' => 'nullable|date',

            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'nullable|exists:productos,id',
            'detalles.*.nombre_producto' => 'required|string',
            'detalles.*.unidad' => 'required|string',
            'detalles.*.factor' => 'required|numeric',
            'detalles.*.precio_unitario' => 'required|numeric',
            'detalles.*.cantidad' => 'required|numeric',
            'detalles.*.subtotal' => 'required|numeric',
            'detalles.*.porcentaje_igv' => 'required|numeric',
            'detalles.*.total_impuestos' => 'nullable|numeric',
            'detalles.*.igv' => 'nullable|numeric',
            'detalles.*.total' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $sucursal = Sucursal::find($request->sucursal_id);
        if (!$sucursal) {
            return response()->json([
                'success' => false,
                'message' => 'La sucursal ya no existe',
            ], 422);
        }

        $modo_venta = $sucursal->modo_venta;
        $negocio_id = $sucursal->negocio_id;

        DB::beginTransaction();

        try {



            $venta = Venta::create([
                'uuid' => Str::uuid(),
                'cliente_id' => $request->cliente_id,
                'nombre_cliente' => $request->nombre_cliente,
                'documento_cliente' => $request->documento_cliente,
                'tipo_documento_cliente' => $request->tipo_documento_cliente,
                'modo_venta' => $modo_venta,
                'estado' => 'pagado',

                'subtotal' => $request->subtotal,
                'igv' => $request->igv,
                'total' => $request->total,
                'total_pagado' => $request->total_pagado,

                'tipo_comprobante_codigo' => $request->tipo_comprobante_codigo,
                'serie_comprobante' => $request->serie_comprobante,
                'correlativo_comprobante' => $request->correlativo_comprobante,

                'sunat_comprobante_pdf' => null,
                'voucher_pdf' => null,
                'sunat_xml_firmado' => null,
                'sunat_cdr' => null,

                'caja_id' => $request->caja_id,
                'sucursal_id' => $request->sucursal_id,

                'fecha_emision' => $request->fecha_emision,
                'fecha_pago' => $request->fecha_pago,
                'negocio_id'=> $negocio_id,
                'tipo_factura'=>'0101' //Por el momento solo venta interna, este campo ya esta relacionado al catalogo 51 de la sunat 
            ]);

            if (!$venta) {
                throw new Exception('No se pudo procesar la venta');
            }

            $metodosPago = $request->metodos_pagos;
            VentaMetodoPago::where('venta_id', $venta->id)->delete();
            // Registrar los métodos de pago en payment_methods
            foreach ($metodosPago as $metodo) {
                VentaMetodoPago::create([
                    'venta_id' => $venta->id,
                    'metodo' => $metodo['codigo'],
                    'monto' => $metodo['monto']
                ]);
            }

            foreach ($request->detalles as $detalle) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $detalle['producto_id'] ?? null,
                    'nombre_producto' => $detalle['nombre_producto'],
                    'categoria_producto' => $detalle['categoria_producto'] ?? null,
                    'unidad' => $detalle['unidad'],
                    'factor' => $detalle['factor'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'cantidad' => $detalle['cantidad'],
                    'subtotal' => $detalle['subtotal'],
                    'porcentaje_igv' => $detalle['porcentaje_igv'],
                    'total_impuestos' => $detalle['total_impuestos'] ?? 0,
                    'igv' => $detalle['igv'] ?? 0,
                    'total' => $detalle['total'],
                    'tipo_afectacion_igv'=> $detalle['tipo_afectacion_igv'],
                ]);
            }

            //$comprobanteGenerado = ComprobanteService::generar($venta->id);
            //dd($comprobanteGenerado);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'venta' => $venta,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
