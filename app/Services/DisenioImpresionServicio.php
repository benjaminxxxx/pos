<?php

namespace App\Services;

use App\Models\Negocio;
use App\Models\Sucursal;
use App\Models\TipoComprobante;
use App\Models\DisenioDisponible;
use App\Models\DisenioImpresion;

class DisenioImpresionServicio
{
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
            ->whereHas('disenioDisponible', function ($q) use ($tipoComprobanteCodigo) {
                $q->where('tipo_comprobante_codigo', $tipoComprobanteCodigo);
            })
            ->first();
    }

    public function guardarConfiguracion($data)
    {
        if ($data['disenio_id'] === 'default') {
            // Si existe un registro, lo eliminamos
            DisenioImpresion::where('negocio_id', $data['negocio_id'])
                ->where('sucursal_id', $data['sucursal_id'])
                ->delete();

            return null; // opcional: indicar que no hay registro personalizado
        }

        // Si no es "default", guardamos/actualizamos
        return DisenioImpresion::updateOrCreate(
            [
                'negocio_id' => $data['negocio_id'],
                'sucursal_id' => $data['sucursal_id'],
            ],
            ['disenio_id' => $data['disenio_id']]
        );
    }

}
