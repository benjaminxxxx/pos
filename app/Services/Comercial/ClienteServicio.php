<?php

namespace App\Services\Comercial;

use App\Models\Cliente;
use Auth;
use Exception;

class ClienteServicio
{
    public static function guardar(array $data): Cliente
    {
        $cuentaId = Auth::user()->cuenta?->id;

        if (!$cuentaId) {
            throw new Exception('No se pudo determinar la cuenta del usuario');
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDACIONES
        |--------------------------------------------------------------------------
        */

        if (empty($data['tipo_cliente_id'])) {
            throw new Exception("Debe indicar el tipo de cliente.");
        }

        if (empty($data['tipo_documento_id'])) {
            throw new Exception("Debe indicar el tipo de documento.");
        }

        if (empty($data['numero_documento'])) {
            throw new Exception("Debe ingresar el número de documento.");
        }

        $tipoCliente = $data['tipo_cliente_id'];
        $tipoDoc = (string) $data['tipo_documento_id'];
        $numeroDoc = trim($data['numero_documento']);

        // -------- EMPRESA --------
        if ($tipoCliente === 'empresa') {

            if ($tipoDoc !== '6') {
                throw new Exception(
                    "Para clientes de tipo Empresa el documento válido es RUC."
                );
            }

            if (!preg_match('/^\d{11}$/', $numeroDoc)) {
                throw new Exception(
                    "El RUC debe contener exactamente 11 dígitos numéricos."
                );
            }
        }

        // -------- PERSONA --------
        if ($tipoCliente === 'persona') {

            if ($tipoDoc === '6') {
                throw new Exception(
                    "Una Persona no puede usar RUC como tipo de documento."
                );
            }

            // DNI
            if ($tipoDoc === '1') {

                if (!preg_match('/^\d{8}$/', $numeroDoc)) {
                    throw new Exception(
                        "El DNI debe contener exactamente 8 dígitos numéricos."
                    );
                }
            }

            // Carnet de extranjería (aprox 9-12 caracteres alfanuméricos)
            if ($tipoDoc === '4') {

                if (!preg_match('/^[A-Za-z0-9]{9,12}$/', $numeroDoc)) {
                    throw new Exception(
                        "El Carnet de Extranjería debe tener entre 9 y 12 caracteres alfanuméricos."
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | GUARDAR CLIENTE
        |--------------------------------------------------------------------------
        */

        $cliente = null;

        if (!empty($data['cliente_id'])) {

            $cliente = Cliente::findOrFail($data['cliente_id']);

            if ($cliente->cuenta_id !== $cuentaId) {
                throw new Exception("No tienes permisos para editar este cliente.");
            }
        }

        $cliente = $cliente ?? new Cliente();

        $cliente->fill($data);
        $cliente->cuenta_id = $cuentaId;
        $cliente->save();

        return $cliente;
    }
    public static function eliminar($id): void
    {
        $cuentaId = Auth::user()->cuenta?->id;

        if (!$cuentaId) {
            throw new Exception("No se pudo determinar la cuenta del usuario.");
        }

        $cliente = Cliente::findOrFail($id);

        if ($cliente->cuenta_id !== $cuentaId) {
            throw new Exception("Este cliente no pertenece a tu cuenta.");
        }

        $cliente->delete();
    }
    public static function listarClientes(array $filtros)
    {
        $cuenta = Auth::user()->cuenta;

        if (!$cuenta) {
            return Cliente::whereRaw('0 = 1');
        }

        $query = Cliente::query()->where('cuenta_id', $cuenta->id);

        // búsqueda
        if (
            !empty($filtros['numero_documento']) ||
            !empty($filtros['nombre_completo']) ||
            !empty($filtros['nombre_comercial']) ||
            !empty($filtros['telefono'])
        ) {
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