<?php

namespace App\Http\Controllers;

use App\Exceptions\StockInsuficienteException;
use App\Services\VentaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Log;

class VentaController extends Controller
{
    public function registrar(Request $request)
    {
        try {
            $venta = VentaServicio::registrarv2($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'venta' => $venta
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verifique los datos ingresados.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (StockInsuficienteException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409); // Conflict: el stock cambió respecto a lo esperado

        } catch (\Exception $e) {
            // Excepciones de negocio "controladas" (correlativo duplicado,
            // cliente faltante, monto no coincide, etc. — todas las que
            // lanzas con `throw new Exception(...)` en guardarVenta)
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {
            // Solo lo verdaderamente inesperado (bug, DB caída, etc.)
            report($e); // deja registro real del error inesperado
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado. Intente nuevamente.',
            ], 500);
        }
    }
   public function listar($sucursal = null)
    {
        try {
            $negocio = Auth::user()->negocio_activo->id;
            $ventas = VentaServicio::listar($negocio,$sucursal);

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
