<?php

namespace App\Http\Controllers;

use App\Services\ProductoServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SucursalController extends Controller
{
    public function sucursalesPorUsuario()
    {
        $user = Auth::user();

        if ($user->hasRole('vendedor')) {
            return response()->json([$user->empleado->sucursal]);
        }

        return response()->json($user->sucursales()
            ->where('sucursales.estado', true)
            ->where('negocios.estado', true)
            ->where('negocios.eliminado', false)
            ->get());
    }
    public function negociosPorUsuario()
    {
        $user = Auth::user();

        if ($user->hasRole('vendedor')) {
            return response()->json([$user->empleado->sucursal->negocio]);
        }

        return response()->json($user->negocios()
            ->with('sucursales')
            ->where('estado', true)
            ->where('eliminado', false)
            ->get());
    }
    /*
    public function buscarProductos(Request $request)
    {
        $sucursal_id = $request->input('sucursal_id');
        $search = $request->input('q');
        $productos = ProductoServicio::buscarPorTextoYStock($search, $sucursal_id);
        return response()->json($productos);
    }*/
    public function buscarProductos(Request $request)
    {
        $negocio_id = $request->input('negocio_id');
        $sucursal_id = $request->input('sucursal_id');
        $search = $request->input('q');

        if (!$negocio_id) {
            return response()->json([
                'error' => 'El parámetro negocio_id es obligatorio.'
            ], 422);
        }

        $productos = ProductoServicio::buscarPorTextoYStock($search, $negocio_id, $sucursal_id);

        return response()->json($productos);
    }
}
