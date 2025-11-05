<?php

namespace App\Traits\DatosUtiles;
use App\Models\Negocio;
use Illuminate\Support\Facades\Session;

trait ConProductos
{
    public $productos = [];
    public function bootConProductos()
    {
        $this->cargarProductos();
    }
    public function cargarProductos()
    {
        $negocioUuid = session('negocio_actual_uuid');

        $negocio = Negocio::where('uuid', $negocioUuid)
            ->with(['productos.presentaciones'])
            ->first();

        if (!$negocio) {
            $this->productos = collect();
            return;
        }

        $productosFinal = [];

        foreach ($negocio->productos as $producto) {
            // Si tiene presentaciones, crear una entrada por cada presentación
            if ($producto->presentaciones->count() > 0) {
                foreach ($producto->presentaciones as $pres) {
                    $productosFinal[] = [
                        'id' => $producto->id . '-' . $pres->id,
                        'producto_id' => $producto->id,
                        'descripcion' => "({$pres->unidad}) {$producto->descripcion} x{$pres->factor}",
                        'codigo' => $pres->codigo_barra ?? $producto->codigo ?? '',
                        'unidad' => $pres->unidad ?? 'NIU',
                        'factor' => $pres->factor ?? 1,
                        'precio' => $pres->precio ?? 0,
                        'precio_mayorista' => $pres->precio_mayorista ?? null,
                        'minimo_mayorista' => $pres->minimo_mayorista ?? null,
                        'tipo' => 'presentacion',
                    ];
                }
            } else {
                // Si no tiene presentaciones, crear solo una versión base
                $productosFinal[] = [
                    'id' => $producto->id,
                    'producto_id' => $producto->id,
                    'descripcion' => "(NIU) " . $producto->descripcion,
                    'codigo' => $producto->codigo ?? '',
                    'unidad' => 'NIU',
                    'factor' => 1,
                    'precio' => $producto->precio ?? 0,
                    'precio_mayorista' => $producto->precio_mayorista ?? null,
                    'minimo_mayorista' => $producto->minimo_mayorista ?? null,
                    'tipo' => 'base',
                ];
            }
        }

        $this->productos = collect($productosFinal);
    }

}