<?php

namespace App\Services\Comercial;

use App\Models\Cliente;
use Auth;
use Exception;

class ClienteServicio
{
    public static function guardar(array $data): Cliente
    {
        $user = Auth::user();

        $dueno_tienda_id = match (true) {
            $user->hasRole('dueno_tienda') => $user->id,
            $user->hasRole('vendedor') => optional($user->dueno_tienda)->id,
            default => null,
        };

        if (!$dueno_tienda_id) {
            throw new \Exception('No se pudo determinar el dueño de tienda');
        }

        $cliente = null;

        if (!empty($data['cliente_id'])) {
            $cliente = Cliente::findOrFail($data['cliente_id']);

            if ($cliente->dueno_tienda_id !== $dueno_tienda_id || !$user->hasRole('dueno_tienda')) {
                throw new \Exception("No tienes permisos para editar este cliente.");
            }
        }

        $cliente = $cliente ?? new Cliente();
        $cliente->fill($data);
        $cliente->dueno_tienda_id = $dueno_tienda_id;
        $cliente->save();

        return $cliente;
    }
    public static function eliminar($id): void
    {
        $user = Auth::user();
        $cliente = Cliente::findOrFail($id);

        if (!$user->hasRole('dueno_tienda')) {
            throw new \Exception("Solo el dueño de tienda puede eliminar clientes.");
        }

        if ($cliente->dueno_tienda_id !== $user->id) {
            throw new \Exception("Este cliente no pertenece a tu tienda.");
        }

        $cliente->delete();
    }
    public static function listarClientes(array $filtros)
    {
        $user = Auth::user();

        $dueno_tienda_id = match (true) {
            $user->hasRole('dueno_tienda') => $user->id,
            $user->hasRole('vendedor') => optional($user->dueno_tienda)->id,
            default => null,
        };

        if (!$dueno_tienda_id) {
            return Cliente::whereRaw('0 = 1');
        }

        $query = Cliente::query()->where('dueno_tienda_id', $dueno_tienda_id);

        // Si hay búsqueda general, hacer OR
        if (!empty($filtros['numero_documento']) || !empty($filtros['nombre_completo']) || !empty($filtros['nombre_comercial']) || !empty($filtros['telefono'])) {
            $query->where(function ($q) use ($filtros) {
                if (!empty($filtros['numero_documento'])) {
                    $q->orWhere('numero_documento', 'like', '%' . $filtros['numero_documento'] . '%');
                }
                if (!empty($filtros['nombre_completo'])) {
                    $q->orWhere('nombre_completo', 'like', '%' . $filtros['nombre_completo'] . '%');
                }
                if (!empty($filtros['nombre_comercial'])) {
                    $q->orWhere('nombre_comercial', 'like', '%' . $filtros['nombre_comercial'] . '%');
                }
                if (!empty($filtros['telefono'])) {
                    $q->orWhere('telefono', 'like', '%' . $filtros['telefono'] . '%');
                }
            });
        }

        if (!empty($filtros['tipo_cliente_id'])) {
            $query->where('tipo_cliente_id', $filtros['tipo_cliente_id']);
        }

        return $query;
    }


}