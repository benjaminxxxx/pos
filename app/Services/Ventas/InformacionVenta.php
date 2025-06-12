<?php

namespace App\Services\Ventas;
use App\Models\Venta;

class InformacionVenta
{
    /**
     * Lista las ventas de un negocio con paginación y filtros opcionales.
     *
     * @param int $negocio_id
     * @param array $filtros ['sucursal_id' => int|null, 'page' => int|null]
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    public static function listarVentas(int $negocio_id, array $filtros = [])
    {
        $query = Venta::with(['detalles','comprobante','notas'])->where('negocio_id', $negocio_id);

        if (!empty($filtros['sucursal_id'])) {
            $query->where('sucursal_id', $filtros['sucursal_id']);
        }

        $perPage = $filtros['perPage'] ?? 10;

        return $query->paginate($perPage);
    }
    /**
     * Elimina una venta por su UUID.
     *
     * @param string $uuid
     */
    public static function eliminarVenta($uuid)
    {
        $venta = Venta::where('uuid', $uuid)->first();
        if (!$venta) {
            throw new \Exception('Venta no encontrada.');
        }
        $venta->delete();
    }
}