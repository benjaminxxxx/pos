<?php

namespace App\Services;
use App\Models\ProductoEntrada;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;


class EntradaProductoServicio
{
    /**
     * Obtener listado de entradas (con filtros opcionales).
     *
     * @param array $filters
     * @return Collection
     */
    public function list(array $filters = []): Collection
    {
        $query = ProductoEntrada::query();

        if (!empty($filters['producto_id'])) {
            $query->where('producto_id', $filters['producto_id']);
        }

        if (!empty($filters['desde'])) {
            $query->whereDate('fecha', '>=', $filters['desde']);
        }

        if (!empty($filters['hasta'])) {
            $query->whereDate('fecha', '<=', $filters['hasta']);
        }

        return $query->orderBy('fecha', 'desc')->get();
    }

    /**
     * Buscar una entrada por id.
     *
     * @param int $id
     * @return ProductoEntrada|null
     */
    public function find(int $id): ?ProductoEntrada
    {
        return ProductoEntrada::find($id);
    }

    /**
     * Crear nueva ProductoEntrada. Lanza ValidationException si falla validación.
     *
     * @param array $data
     * @return ProductoEntrada
     * @throws ValidationException
     */
    public function crear(array $data): ProductoEntrada
    {
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return DB::transaction(function () use ($validator) {
            $validated = $validator->validated();

            // stock_disponible inicialmente igual a cantidad ingresada
            $validated['stock_disponible'] = $validated['cantidad'];
            $validated['created_by'] = auth()->id();

            return ProductoEntrada::create($validated);
        });
    }

    /**
     * Actualizar una entrada existente. Lanza ValidationException si falla validación.
     *
     * @param int $id
     * @param array $data
     * @return ProductoEntrada
     * @throws ValidationException|ModelNotFoundException
     */
    public function update(int $id, array $data): ProductoEntrada
    {
        $entrada = $this->findOrFail($id);

        $validator = Validator::make($data, $this->rules($id));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        DB::transaction(function () use ($entrada, $validator) {
            $entrada->update($validator->validated());
        });

        return $entrada->refresh();
    }

    /**
     * Eliminar una entrada.
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $entrada = $this->findOrFail($id);
        return $entrada->delete();
    }

    /**
     * Obtener reglas de validación. $id es opcional para reglas únicas cuando aplica.
     *
     * @param int|null $id
     * @return array
     */
    protected function rules(?int $id = null): array
    {
        return [
            'fecha_ingreso' => 'required|date',
            'producto_id' => 'required|integer|exists:productos,id',
            'sucursal_id' => 'required|integer|exists:sucursales,id',
            'tipo_entrada' => 'required|string|max:30',
            'cantidad' => 'required|numeric|min:0.001',
            'costo_unitario' => 'required|numeric|min:0',
        ];
    }

    /**
     * Buscar entrada o lanzar ModelNotFoundException.
     *
     * @param int $id
     * @return ProductoEntrada
     * @throws ModelNotFoundException
     */
    protected function findOrFail(int $id): ProductoEntrada
    {
        return ProductoEntrada::findOrFail($id);
    }
}