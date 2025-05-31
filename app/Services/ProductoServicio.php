<?php
// app/Services/ProductoServicio.php

namespace App\Services;

use App\Models\Producto;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intervention\Image\Laravel\Facades\Image;


class ProductoServicio
{
    public static function buscar(array $filtros)
    {
        return Producto::query()
            ->where('negocio_id', $filtros['negocio_id'])
            ->when($filtros['search'], function ($query) use ($filtros) {
                $query->where(function ($q) use ($filtros) {
                    $q->where('descripcion', 'like', '%' . $filtros['search'] . '%')
                        ->orWhere('detalle', 'like', '%' . $filtros['search'] . '%')
                        ->orWhere('codigo_barra', 'like', '%' . $filtros['search'] . '%');
                });
            })
            ->when($filtros['categoria_id'], fn($query) => $query->where('categoria_id', $filtros['categoria_id']))
            ->when($filtros['marca_id'], fn($query) => $query->where('marca_id', $filtros['marca_id']))
            ->when($filtros['activo'] !== '', fn($query) => $query->where('activo', $filtros['activo']))
            ->orderBy('descripcion')
            ->paginate(10);
    }
    public static function obtenerProductoPorUuid($uuid)
    {
        $producto = Producto::where('uuid', $uuid)->firstOrFail();

        // Verificar permisos
        if (Auth::user()->hasRole('vendedor') && $producto->creado_por != Auth::id()) {
            if ($producto->tieneVentas()) {
                throw new UnauthorizedException('No puedes editar un producto que ya tiene ventas registradas.');
            }
        }

        return $producto;
    }
    public static function guardar(array $data)
    {

        // Eliminar imagen anterior si existía y está marcada como eliminada
        if (!empty($data['imagen_a_eliminar']) && $data['imagen_eliminada']) {

            Storage::disk('public')->delete($data['imagen_a_eliminar']);
            $data['imagen_url'] = null;
        }

        if (isset($data['imagen']) && $data['imagen']) {
            $path = "productos/" . date('Y') . '/' . date('m');
            $filename = Str::random(20) . '.' . $data['imagen']->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }

            $image = Image::read($data['imagen'])->scale(500);
            $publicPath = Storage::disk('public')->path($path . '/' . $filename);
            $image->save($publicPath);



            $data['imagen_url'] = $path . '/' . $filename;
        }

        // Crear o actualizar producto
        $producto = isset($data['producto_id']) && $data['producto_id']
            ? Producto::findOrFail(id: $data['producto_id'])
            : new Producto(['uuid' => Str::uuid(), 'creado_por' => Auth::id()]);

        $porcentaje = ($data['porcentaje_igv'] ?? 0) / 100;
        $factor = 1 + $porcentaje;

        $data['monto_venta_sinigv'] = round($data['monto_venta'] / $factor, 2);
        $data['monto_compra_sinigv'] = round($data['monto_compra'] / $factor, 2);

        $producto->fill([
            'codigo_barra' => $data['codigo_barra'] ?? null,
            'sunat_code' => $data['sunat_code'] ?? null,
            'descripcion' => $data['descripcion'],
            'detalle' => $data['detalle'] ?? null,
            'imagen_path' => $data['imagen_url'] ?? null,
            'porcentaje_igv' => $data['porcentaje_igv'],
            'monto_venta' => $data['monto_venta'],
            'monto_venta_sinigv' => $data['monto_venta_sinigv'],
            'monto_compra' => $data['monto_compra'],
            'monto_compra_sinigv' => $data['monto_compra_sinigv'],
            'unidad' => $data['unidad'],
            'tipo_afectacion_igv' => $data['tipo_afectacion_igv'],
            'categoria_id' => $data['categoria_id'] ?? null,
            'marca_id' => $data['marca_id'] ?? null,
            'negocio_id' => $data['negocio_id'],
            'activo' => $data['activo'] ?? true,
        ]);

        $producto->save();

        // Actualizar presentaciones
        $producto->presentaciones()->delete();
        if (isset($data['presentaciones']) && is_array($data['presentaciones'])) {

            foreach ($data['presentaciones'] as $presentacion) {
                $producto->presentaciones()->create([
                    'codigo_barra' => $presentacion['codigo_barra'] ?? null,
                    'unidad' => $presentacion['unidad'],
                    'descripcion' => $presentacion['descripcion'],
                    'factor' => $presentacion['factor'],
                    'precio' => $presentacion['precio'],
                    'activo' => true,
                ]);
            }
        }


        // Stocks
        if (isset($data['stocks']) && is_array($data['stocks'])) {
            foreach ($data['stocks'] as $sucursal_id => $stock) {
                Stock::updateOrCreate(
                    [
                        'producto_id' => $producto->id,
                        'sucursal_id' => $sucursal_id,
                    ],
                    [
                        'cantidad' => $stock['cantidad'],
                        'stock_minimo' => $stock['stock_minimo'],
                    ]
                );
            }
        }

        return $producto;
    }
    public static function eliminarProductoPorUuid(string $uuid): void
    {
        $producto = Producto::where('uuid', $uuid)->first();

        if (!$producto) {
            throw new ModelNotFoundException("Producto no encontrado.");
        }

        $user = Auth::user();

        if ($user->hasRole('vendedor')) {
            if ($producto->creado_por !== $user->id || $producto->tieneVentas()) {
                throw new UnauthorizedException("No tienes permisos para eliminar este producto.");
            }
        }

        if ($producto->imagen_path) {
            Storage::disk('public')->delete($producto->imagen_path);
        }

        $producto->presentaciones()->delete();
        $producto->stocks()->delete();
        $producto->delete();
    }
}
