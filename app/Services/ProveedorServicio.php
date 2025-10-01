<?php

namespace App\Services;

use App\Models\Proveedor;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Servicio para gestionar la lógica de negocio de los proveedores.
 */
class ProveedorServicio
{
    /**
     * Busca un proveedor por su UUID dentro de una cuenta específica.
     * Permite buscar también en los registros eliminados (soft deleted).
     *
     * @param string $uuid
     * @param int $cuentaId
     * @param bool $withTrashed
     * @return Proveedor|null
     */
    public function obtenerPorUuid(string $uuid, int $cuentaId, bool $withTrashed = false): ?Proveedor
    {
        $query = $withTrashed ? Proveedor::withTrashed() : Proveedor::query();
        return $query->where('uuid', $uuid)
                     ->where('cuenta_id', $cuentaId)
                     ->first();
    }

    /**
     * Crea un nuevo proveedor o actualiza uno existente.
     *
     * @param array $data Los datos del proveedor.
     * @param int|null $proveedorId El ID del proveedor a actualizar, o null para crear uno nuevo.
     * @return Proveedor
     */
    public function crearOActualizar(array $data, ?int $proveedorId): Proveedor
    {
        if ($proveedorId) {
            $proveedor = Proveedor::findOrFail($proveedorId);
            $data['updated_by'] = auth()->id();
            $proveedor->update($data);
            return $proveedor;
        }

        $data['created_by'] = auth()->id();
        return Proveedor::create($data);
    }

    /**
     * Elimina (soft delete) un proveedor, registrando quién lo eliminó.
     *
     * @param string $uuid
     * @param int $cuentaId
     * @return bool Retorna true si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminar(string $uuid, int $cuentaId): bool
    {
        $proveedor = $this->obtenerPorUuid($uuid, $cuentaId);
        if ($proveedor) {
            $proveedor->deleted_by = auth()->id();
            $proveedor->save();
            return $proveedor->delete();
        }
        return false;
    }

    /**
     * Restaura un proveedor que fue eliminado previamente.
     *
     * @param string $uuid
     * @param int $cuentaId
     * @return bool Retorna true si la restauración fue exitosa, false en caso contrario.
     */
    public function restaurar(string $uuid, int $cuentaId): bool
    {
        $proveedor = $this->obtenerPorUuid($uuid, $cuentaId, true);
        if ($proveedor && $proveedor->trashed()) {
            return $proveedor->restore();
        }
        return false;
    }

    /**
     * Obtiene una lista paginada y filtrada de proveedores para una cuenta.
     *
     * @param int $cuentaId
     * @param string $filtroNombre
     * @param string $filtroEstado
     * @param string $filtroEliminados
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listarPaginado(int $cuentaId, string $filtroNombre, string $filtroEstado, string $filtroEliminados, int $perPage = 20): LengthAwarePaginator
    {
        $query = Proveedor::where('cuenta_id', $cuentaId);

        if (!empty($filtroNombre)) {
            $query->where(function ($q) use ($filtroNombre) {
                $q->where('razon_social', 'like', '%' . $filtroNombre . '%')
                  ->orWhere('nombre_comercial', 'like', '%' . $filtroNombre . '%');
            });
        }

        if ($filtroEstado !== '') {
            $query->where('estado', $filtroEstado);
        }

        if ($filtroEliminados === 'Eliminados') {
            $query->onlyTrashed();
        }

        return $query->orderBy('razon_social')->paginate($perPage);
    }
}