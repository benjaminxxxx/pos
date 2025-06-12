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

        return response()->json($user->sucursales()->where('estado', true)->get());
    }
    public function buscarProductos(Request $request)
    {
        $sucursal_id = $request->input('sucursal_id');
        $search = $request->input('q');
        $productos = ProductoServicio::buscarPorTextoYStock($search, $sucursal_id);
        return response()->json($productos);
    }
}
