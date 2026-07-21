<?php

namespace App\Services;

use App\Models\ProductoEntrada;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EntradaProductoServicio
{
    public function __construct(private readonly StockService $stockService)
    {
    }

    public function list(array $filters = []): Collection
    {
        $query = ProductoEntrada::query();

        if (!empty($filters['producto_id'])) {
            $query->where('producto_id', $filters['producto_id']);
        }
        if (!empty($filters['sucursal_id'])) {
            $query->where('sucursal_id', $filters['sucursal_id']);
        }
        if (!empty($filters['desde'])) {
            $query->whereDate('fecha_ingreso', '>=', $filters['desde']);
        }
        if (!empty($filters['hasta'])) {
            $query->whereDate('fecha_ingreso', '<=', $filters['hasta']);
        }

        return $query->orderBy('fecha_ingreso', 'desc')->get();
    }

    public function find(int $id): ?ProductoEntrada
    {
        return ProductoEntrada::find($id);
    }

    /**
     * Crea una entrada y aumenta el stock. Es idempotente cuando viene
     * acompañada de referencia_id + referencia_tipo (ej. reparaciones que
     * reingresan repuesto): si ya existe una entrada para esa referencia
     * + producto + sucursal, se retorna sin duplicar el movimiento.
     *
     * @throws ValidationException
     */
    public function generarEntrada(array $data): ProductoEntrada
    {
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated) {

            if (!empty($validated['referencia_id']) && !empty($validated['referencia_tipo'])) {
                $existente = ProductoEntrada::where('referencia_id', $validated['referencia_id'])
                    ->where('referencia_tipo', $validated['referencia_tipo'])
                    ->where('producto_id', $validated['producto_id'])
                    ->where('sucursal_id', $validated['sucursal_id'])
                    ->lockForUpdate()
                    ->first();

                if ($existente) {
                    return $existente;
                }
            }

            $validated['stock_disponible'] = $validated['cantidad'];
            $validated['created_by'] = auth()->id();

            $entrada = ProductoEntrada::create($validated);

            $this->stockService->incrementar(
                $entrada->producto_id,
                $entrada->sucursal_id,
                $entrada->cantidad
            );

            return $entrada;
        });
    }

    /**
     * Anula una entrada (solo para corregir un error de captura inmediato,
     * no como flujo normal de negocio) y revierte el stock que aportó.
     */
    public function anular(int $id): void
    {
        DB::transaction(function () use ($id) {
            $entrada = ProductoEntrada::lockForUpdate()->findOrFail($id);

            $this->stockService->decrementar(
                $entrada->producto_id,
                $entrada->sucursal_id,
                $entrada->stock_disponible
            );

            $entrada->delete();
        });
    }

    protected function rules(): array
    {
        return [
            'fecha_ingreso'     => 'required|date',
            'producto_id'       => 'required|integer|exists:productos,id',
            'sucursal_id'       => 'required|integer|exists:sucursales,id',
            'tipo_entrada'      => 'required|string|max:30',
            'cantidad'          => 'required|numeric|min:0.001',
            'costo_unitario'    => 'required|numeric|min:0',
            'referencia_id'     => 'nullable|integer|required_with:referencia_tipo',
            'referencia_tipo'   => 'nullable|string|max:50|required_with:referencia_id',
        ];
    }
}