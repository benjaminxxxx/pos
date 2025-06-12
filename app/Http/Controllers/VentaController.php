<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\VentaMetodoPago;
use App\Services\VentaServicio;
use Exception;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
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
