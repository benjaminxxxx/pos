<?php

namespace App\Services;

use App\Models\Negocio;
use App\Models\Sucursal;
use App\Models\TipoComprobante;
use App\Models\DisenioDisponible;
use App\Models\DisenioImpresion;

class DisenioImpresionServicio
{
    public function guardarOpcionesConfiguracion($data)
    {
        return DisenioImpresion::updateOrCreate(
            [
                'negocio_id' => $data['negocio_id'],
                'sucursal_id' => $data['sucursal_id'],
                'disenio_id' => $data['disenio_id'],
            ],
            $data
        );
    }

    public function getDefaultConfig($tipoComprobanteCodigo)
    {
        return DisenioDisponible::where('tipo_comprobante_codigo', $tipoComprobanteCodigo)
            ->where('codigo', 'default')
            ->firstOrFail();
    }

    public function getNegocios($userId)
    {
        return Negocio::where('user_id', $userId)
            ->with('sucursales')
            ->get();
    }

    public function getSucursales($negocioId)
    {
        return Sucursal::where('negocio_id', $negocioId)->get();
    }

    public function getTiposComprobante()
    {
        return TipoComprobante::where('estado', true)->get();
    }

    public function getDisenosDisponibles($tipoComprobanteCodigo)
    {
        return DisenioDisponible::where('tipo_comprobante_codigo', $tipoComprobanteCodigo)
            ->where('activo', true)
            ->get();
    }

    public function getConfiguracion($negocioId, $sucursalId, $tipoComprobanteCodigo)
    {
        return DisenioImpresion::where('negocio_id', $negocioId)
            ->where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->whereHas('disenioDisponible', function ($q) use ($tipoComprobanteCodigo) {
                $q->where('tipo_comprobante_codigo', $tipoComprobanteCodigo);
            })
            ->first();
    }


    public function guardarConfiguracion(array $data)
    {
        // Traemos el diseño base para conocer qué tipo de comprobante es
        $disenioBase = DisenioDisponible::find($data['disenio_id']);

        if (!$disenioBase && $data['disenio_id'] !== 'default') {
            throw new \Exception("El diseño seleccionado no existe.");
        }

        $tipoComprobante = $disenioBase
            ? $disenioBase->tipo_comprobante_codigo
            : $data['tipo_comprobante_codigo']; // cuando es default, el componente te manda este dato

        // 1. DESACTIVAR SOLO LOS DISEÑOS DEL MISMO TIPO DE COMPROBANTE
        DisenioImpresion::where('negocio_id', $data['negocio_id'])
            ->where('sucursal_id', $data['sucursal_id'])
            ->whereHas('disenioDisponible', function ($q) use ($tipoComprobante) {
                $q->where('tipo_comprobante_codigo', $tipoComprobante);
            })
            ->update(['activo' => false]);

        // 2. SI ES DEFAULT → no activamos ninguno
        if ($data['disenio_id'] === 'default') {
            return null;
        }

        // 3. GUARDAR/ACTUALIZAR EL DISEÑO ACTUAL COMO ACTIVO
        return DisenioImpresion::updateOrCreate(
            [
                'negocio_id' => $data['negocio_id'],
                'sucursal_id' => $data['sucursal_id'],
                'disenio_id' => $data['disenio_id'],
            ],
            array_merge($data, [
                'activo' => true,
            ])
        );
    }


}
