<?php

namespace App\Services;
use App\Models\ProductoSalida;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class SalidaProductoServicio
{
    protected function rules(?int $id = null): array
    {
        return [
            'fecha_salida' => 'required|date',
            'producto_id' => 'required|integer|exists:productos,id',
            'sucursal_id' => 'required|integer|exists:sucursales,id',
            'tipo_salida' => 'required|string|max:30',
            'cantidad' => 'required|numeric|min:0.001',
            'costo_unitario' => 'required|numeric|min:0',
        ];
    }
    public function generarSalida(array $data)
    {
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $salidaExistente = ProductoSalida::where('referencia_id', $data['referencia_id'])
            ->where('referencia_tipo', $data['referencia_tipo'])
            ->where('producto_id', $data['producto_id'])
            ->where('sucursal_id', $data['sucursal_id'])
            ->first();

        if ($salidaExistente) {
            return $salidaExistente;
        }

        return ProductoSalida::create($data);
    }
}