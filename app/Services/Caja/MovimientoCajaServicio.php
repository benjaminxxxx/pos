<?php

namespace App\Services\Caja;

use App\Models\MovimientoCaja;
use App\Models\TipoMovimiento;
use Illuminate\Support\Facades\DB;
use Exception;

class MovimientoCajaServicio
{
    /**
     * Registra un movimiento de caja.
     * Útil para Ventas, Compras o registros manuales.
     */
    public function registrar(array $data)
    {
        return MovimientoCaja::create([
            'tipo_movimiento_id' => $data['tipo_movimiento_id'],
            'cuenta_id' => $data['cuenta_id'],
            'sucursal_id' => $data['sucursal_id'],
            'usuario_id' => $data['usuario_id'],
            'monto' => $data['monto'],
            'metodo_pago' => $data['metodo_pago'] ?? null,
            'observacion' => $data['observacion'] ?? null,
            'fecha' => $data['fecha'] ?? now(),
            'referencia_tipo' => $data['referencia_tipo'] ?? null,
            'referencia_id' => $data['referencia_id'] ?? null,
        ]);
    }

    /**
     * Anula un movimiento creando una contrapartida (mismo monto, efecto contrario).
     */
    public function anular(int $movimientoId, int $usuarioId, string $motivo)
    {
        return DB::transaction(function () use ($movimientoId, $usuarioId, $motivo) {
            $original = MovimientoCaja::findOrFail($movimientoId);

            // Evitar doble anulación si ya existe una referencia de anulación
            // (Opcional: podrías marcar el original como 'anulado' en un campo booleano si lo deseas)

            return MovimientoCaja::create([
                'tipo_movimiento_id' => $original->tipo_movimiento_id,
                'cuenta_id' => $original->cuenta_id,
                'sucursal_id' => $original->sucursal_id,
                'usuario_id' => $usuarioId,
                'monto' => $original->monto * -1, // Contrapartida
                'metodo_pago' => $original->metodo_pago,
                'referencia_tipo' => 'anulacion',
                'referencia_id' => $original->id,
                'observacion' => "ANULACIÓN de mov #{$original->id}. Motivo: {$motivo}",
                'fecha' => now(),
            ]);
        });
    }

    /**
     * Solo permite editar campos no críticos.
     */
    public function actualizarCamposPermitidos(int $movimientoId, array $data)
    {
        $movimiento = MovimientoCaja::findOrFail($movimientoId);

        $movimiento->update([
            'observacion' => $data['observacion'] ?? $movimiento->observacion,
            'metodo_pago' => $data['metodo_pago'] ?? $movimiento->metodo_pago,
            'fecha' => $data['fecha'] ?? $movimiento->fecha,
        ]);

        return $movimiento;
    }
    public function listar(array $filtros, int $cuentaId)
    {
        $query = MovimientoCaja::query()
            ->where('cuenta_id', $cuentaId)
            ->with(['tipoMovimiento', 'sucursal', 'usuario'])
            ->latest('fecha'); // Ordenar por fecha más reciente por defecto

        // Filtro por tipo de flujo (ingreso/egreso) mediante relación
        if (!empty($filtros['flujo'])) {
            $query->whereHas('tipoMovimiento', function ($q) use ($filtros) {
                $q->where('tipo_flujo', $filtros['flujo']);
            });
        }

        // Filtro por tipo de movimiento específico
        if (!empty($filtros['tipo_id'])) {
            $query->where('tipo_movimiento_id', $filtros['tipo_id']);
        }

        // Filtro por fecha específica o rangos
        if (!empty($filtros['fecha'])) {
            $query->whereDate('fecha', $filtros['fecha']);
        }

        // Filtro por sucursal (si lo necesitaras en el futuro)
        if (!empty($filtros['sucursal_id'])) {
            $query->where('sucursal_id', $filtros['sucursal_id']);
        }

        return $query;
    }
}