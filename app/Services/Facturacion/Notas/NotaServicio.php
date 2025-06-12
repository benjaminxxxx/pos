<?php

namespace App\Services\Facturacion\Notas;

use App\Models\Nota;
use InvalidArgumentException;

class NotaServicio
{
    public static function generarNotaCredito(array $datos): Nota
    {
        $requeridos = [
            'tipoDoc',
            'tipoDocAfectado',
            'numDocAfectado',
            'tipoNota',
            'fechaEmision',
            'motivo',
            'negocio_id',
            'venta_id',
        ];

        // Validar requeridos
        foreach ($requeridos as $campo) {
            if (empty($datos[$campo])) {
                throw new InvalidArgumentException(message: "El campo {$campo} es obligatorio para generar una nota de crédito.");
            }
        }

        // Campos mapeados (obligatorios + opcionales con null por defecto)
        $campos = [
            'tipo_doc' => $datos['tipoDoc'] ?? null,
            'serie' => $datos['serie'] ?? null,
            'correlativo' => $datos['correlativo'] ?? null,
            'fecha_emision' => $datos['fechaEmision'],
            'tip_doc_afectado' => $datos['tipoDocAfectado'],
            'num_doc_afectado' => $datos['numDocAfectado'],
            'cod_motivo' => $datos['tipoNota'],
            'des_motivo' => $datos['motivo'],
            'tipo_moneda' => $datos['tipoMoneda'] ?? 'PEN',
            'mto_oper_gravadas' => $datos['mtoOperGravadas'] ?? 0,
            'mto_igv' => $datos['mtoIgv'] ?? null,
            'total_impuestos' => $datos['totalImpuestos'] ?? null,
            'mto_imp_venta' => $datos['mtoImpVenta'] ?? null,
            'cliente_id' => $datos['clienteId'] ?? null,
            'empresa_id' => $datos['empresaId'] ?? null,
            'forma_pago' => $datos['formaPago'] ?? null,
            'cuotas' => $datos['cuotas'] ?? null,
            'guias' => $datos['guias'] ?? null,
            'negocio_id' => $datos['negocio_id'] ?? null,
            'sucursal_id' => $datos['sucursal_id'],
            'venta_id' => $datos['venta_id'] ?? null,
        ];

        return Nota::create($campos);
    }
}