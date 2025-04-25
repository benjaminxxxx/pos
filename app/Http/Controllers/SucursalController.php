<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Buscar productos que coincidan en código o nombre
        $productos = Producto::where(function ($query) use ($search) {
            $query->where('codigo_barra', 'like', '%' . $search . '%')
                ->orWhere('nombre_producto', 'like', '%' . $search . '%')
                ->orWhereHas('presentaciones', function ($q) use ($search) {
                    $q->where('codigo_barra', 'like', '%' . $search . '%');
                });
        })
            ->with([
                'presentaciones' => function ($q) {
                    $q->where('activo', true); // Solo presentaciones activas
                }
            ,'categoria'])
            ->get()
            ->map(function ($producto) use ($sucursal_id) {
                // Consultar stock en esa sucursal
                $stock = DB::table('stocks')
                    ->where('producto_id', $producto->id)
                    ->where('sucursal_id', $sucursal_id)
                    ->value('cantidad');

                $producto->stock = $stock ?? 0;

                return $producto;
            });

        return response()->json($productos);
    }
}
