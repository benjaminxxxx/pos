<?php

namespace App\Services\Facturacion\Configuracion;

use App\Models\Correlativo;
use App\Models\Sucursal;
use Auth;
use DB;

class CorrelativoServicio
{
    public static function listarPorNegocio($negocioId)
    {
        $user = Auth::user();

        // Validar que el usuario tenga el rol de dueno_tienda
        if (!$user->hasRole('dueno_tienda')) {
            throw new \Exception("Acceso no habilitado para este tipo de usuario.");
        }

        // Verificar que el negocio pertenezca al usuario
        $negocio = $user->negocios()->where('id', $negocioId)->first();

        if (!$negocio) {
            throw new \Exception("No tienes acceso a este negocio.");
        }

        return Correlativo::with(['tipoComprobante', 'sucursales'])
            ->where('negocio_id', $negocioId)
            ->get();
    }

    public static function guardar(array $data, ?Correlativo $correlativo = null): Correlativo
    {
        $user = Auth::user();

        if (!$user->hasRole('dueno_tienda')) {
            throw new \Exception("Solo el dueño de tienda puede crear o editar correlativos.");
        }

        $negocio = $user->negocios()->where('id', $data['negocio_id'])->first();

        if (!$negocio) {
            throw new \Exception("No tienes acceso al negocio seleccionado.");
        }

        return DB::transaction(function () use ($data, $correlativo) {
            $sucursalIds = $data['sucursales'] ?? [];

            // Validar que ninguna de las sucursales ya esté asociada a otro correlativo del mismo tipo en el mismo negocio
            $conflicto = Correlativo::where('negocio_id', $data['negocio_id'])
                ->where('tipo_comprobante_codigo', $data['tipo_comprobante_codigo'])
                ->when($correlativo?->id, fn($q) => $q->where('id', '!=', $correlativo->id))
                ->whereHas('sucursales', function ($q) use ($sucursalIds) {
                    $q->whereIn('sucursal_id', $sucursalIds);
                })
                ->exists();

            if ($conflicto) {
                throw new \Exception("Ya existe un correlativo de este tipo asignado a una o más de las sucursales seleccionadas.");
            }

            // Crear si no existe
            if (!$correlativo) {
                $correlativo = new Correlativo();
            }

            $correlativo->tipo_comprobante_codigo = $data['tipo_comprobante_codigo'];
            $correlativo->serie = strtoupper($data['serie']);
            $correlativo->correlativo_actual = $data['correlativo_actual'];
            $correlativo->estado = $data['estado'] ?? true;
            $correlativo->negocio_id = $data['negocio_id'];
            $correlativo->save();

            // Sincronizar sucursales si vienen
            if (!empty($sucursalIds)) {
                $correlativo->sucursales()->sync($sucursalIds);
            }

            return $correlativo;
        });
    }

    public static function eliminar(int $correlativoId): void
    {
        $user = Auth::user();

        if (!$user->hasRole('dueno_tienda')) {
            throw new \Exception("No tienes permiso para eliminar correlativos.");
        }

        $correlativo = Correlativo::with('negocio')->find($correlativoId);

        if (!$correlativo) {
            throw new \Exception("El correlativo no existe.");
        }

        // PATCH: Se actualizó la comparación estricta del ID de usuario entre $correlativo->negocio->user_id (string en servidor) 
        // y $user->id (integer local) para evitar errores de acceso debido a la diferencia de tipos en distintos entornos.
        // Solución: Se fuerza ambos valores a integer antes de comparar.

        if ((int) $correlativo->negocio->user_id !== (int) $user->id) {
            throw new \Exception("No tienes acceso a este correlativo.");
        }

        DB::transaction(function () use ($correlativo) {
            $correlativo->sucursales()->detach();
            $correlativo->delete();
        });
    }

}