<?php
namespace App\Services;

use App\Models\SucursalCorrelativo;
use Exception;

class CorrelativoServicio
{
    protected $correlativoModel;

    public function obtenerNumeracion($sucursalId, $tipoComprobanteCodigo)
    {
        $correlativos = SucursalCorrelativo::with('correlativo')
            ->where('sucursal_id', $sucursalId)
            ->whereHas('correlativo', function ($q) use ($tipoComprobanteCodigo) {
                $q->where('tipo_comprobante_codigo', $tipoComprobanteCodigo);
            })
            ->get();

        if ($correlativos->count() === 0) {
            throw new Exception("No se encontró configuración de serie y correlativo para el tipo de comprobante [$tipoComprobanteCodigo] en la sucursal [$sucursalId].");
        }

        if ($correlativos->count() > 1) {
            throw new Exception("Existe más de una configuración de correlativo para el tipo de comprobante [$tipoComprobanteCodigo] en la sucursal [$sucursalId]. Debe haber solo una.");
        }

        $this->correlativoModel = $correlativos->first()->correlativo;

        if (!$this->correlativoModel) {
            throw new Exception("No se pudo obtener el correlativo asociado.");
        }

        return [
            'serie' => $this->correlativoModel->serie,
            'correlativo' => $this->correlativoModel->correlativo_actual + 1,
        ];
    }

    public function guardarCorrelativo()
    {
        if (!$this->correlativoModel) {
            throw new Exception("No hay correlativo pendiente para guardar. Llama a obtenerNumeracion() primero.");
        }

        $this->correlativoModel->increment('correlativo_actual');
    }
}
