<?php

namespace App\Http\Controllers;

use App\Services\VentaServicio;
use Illuminate\Http\Request;
use Log;

class VentaController extends Controller
{
    public function registrar(Request $request)
    {
        try {
            $venta = VentaServicio::registrar($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'venta' => $venta
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
   public function listar($sucursal)
    {
        try {
            $ventas = VentaServicio::listar($sucursal);

            return response()->json([
                'success' => true,
                'ventas' => $ventas
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
