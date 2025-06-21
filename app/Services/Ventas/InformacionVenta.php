<?php

namespace App\Services\Ventas;
use App\Models\Sucursal;
use App\Models\Venta;
use Illuminate\Support\Carbon;

class InformacionVenta
{
    public static function obtenerTarjetasEstadisticas(string $filtro): array
    {
        [$tipo, $id] = explode('-', $filtro) + [null, null];
        $hoy = Carbon::now()->toDateString();
        $ayer = Carbon::yesterday()->toDateString();

        $query = Venta::query()->where('estado', 'pagado');

        if ($filtro === 'general') {
            $user = auth()->user();

            if ($user->hasRole('dueno_tienda')) {
                // Obtener los IDs de sus propios negocios y sucursales
                $negocioIds = $user->negocios()->pluck('id'); // Asumiendo que hay relación definida
                $sucursalIds = Sucursal::whereIn('negocio_id', $negocioIds)->pluck('id');

                $query->whereIn('sucursal_id', $sucursalIds);
            } elseif ($user->hasRole('dueno_sistema')) {
                // Acceso completo, no se filtra nada
            } else {
                throw new \Exception("Sin permisos para acceder al resumen general.");
            }
        } elseif ($tipo === 'negocio') {
            $query->where('negocio_id', $id);
        } elseif ($tipo === 'sucursal') {
            $query->where('sucursal_id', $id);
        } else {
            throw new \Exception("Filtro no válido o sin permisos");
        }

        // Ventas hoy y ayer
        $ventasHoy = (clone $query)->whereDate('fecha_emision', $hoy)->sum('monto_importe_venta');
        $ventasAyer = (clone $query)->whereDate('fecha_emision', $ayer)->sum('monto_importe_venta');

        // Transacciones hoy y ayer
        $transHoy = (clone $query)->whereDate('fecha_emision', $hoy)->count();
        $transAyer = (clone $query)->whereDate('fecha_emision', $ayer)->count();

        // Clientes atendidos hoy y ayer
        $clientesHoy = (clone $query)->whereDate('fecha_emision', $hoy)->whereNotNull('cliente_id')->distinct('cliente_id')->count('cliente_id');
        $clientesAyer = (clone $query)->whereDate('fecha_emision', $ayer)->whereNotNull('cliente_id')->distinct('cliente_id')->count('cliente_id');

        // Ticket promedio = ventasHoy / transHoy (si transHoy > 0)
        $ticketHoy = $transHoy > 0 ? $ventasHoy / $transHoy : 0;
        $ticketAyer = $transAyer > 0 ? $ventasAyer / $transAyer : 0;

        return [
            [
                'title' => 'VENTAS HOY',
                'value' => 'S/. ' . number_format($ventasHoy, 2),
                'change' => self::calcularCambio($ventasHoy, $ventasAyer),
                'trend' => self::calcularTrend($ventasHoy, $ventasAyer),
                'icon' => 'dollar',
                'color' => 'text-green-600',
            ],
            [
                'title' => 'TRANSACCIONES',
                'value' => number_format($transHoy),
                'change' => self::calcularCambio($transHoy, $transAyer),
                'trend' => self::calcularTrend($transHoy, $transAyer),
                'icon' => 'cart',
                'color' => 'text-blue-600',
            ],
            [
                'title' => 'CLIENTES ATENDIDOS',
                'value' => number_format($clientesHoy),
                'change' => self::calcularCambio($clientesHoy, $clientesAyer),
                'trend' => self::calcularTrend($clientesHoy, $clientesAyer),
                'icon' => 'users',
                'color' => 'text-purple-600',
            ],
            [
                'title' => 'TICKET PROMEDIO',
                'value' => 'S/. ' . number_format($ticketHoy, 2),
                'change' => self::calcularCambio($ticketHoy, $ticketAyer),
                'trend' => self::calcularTrend($ticketHoy, $ticketAyer),
                'icon' => 'trend',
                'color' => 'text-orange-600',
            ],
        ];
    }
    public static function obtenerVentasSemanales(string $filtro): array
    {
        $query = Venta::query()->where('estado', 'pagado');

        if ($filtro === 'general') {
            $user = auth()->user();

            if ($user->hasRole('dueno_tienda')) {
                $negocioIds = $user->negocios()->pluck('id');
                $sucursalIds = \App\Models\Sucursal::whereIn('negocio_id', $negocioIds)->pluck('id');

                $query->whereIn('sucursal_id', $sucursalIds);
            } elseif (!$user->hasRole('dueno_sistema')) {
                throw new \Exception('Sin permisos para acceder a datos generales.');
            }

        } else {
            [$tipo, $id] = explode('-', $filtro);

            if ($tipo === 'negocio') {
                $query->where('negocio_id', $id);
            } elseif ($tipo === 'sucursal') {
                $query->where('sucursal_id', $id);
            } else {
                throw new \Exception('Filtro no válido.');
            }
        }

        // Fechas de la semana actual (lunes a domingo)
        $inicioSemana = now()->startOfWeek();
        $finSemana = now()->endOfWeek();

        $ventasPorDia = $query->whereBetween('fecha_emision', [$inicioSemana, $finSemana])
            ->get()
            ->groupBy(fn($venta) => ucfirst(Carbon::parse($venta->fecha_emision)->locale('es')->isoFormat('ddd')))
            ->map(fn($ventas) => [
                'day' => ucfirst(Carbon::parse($ventas->first()->fecha_emision)->locale('es')->isoFormat('ddd')),
                'sales' => $ventas->sum('monto_importe_venta'),
            ])
            ->values();

        $diasSemana = ['Lun.', 'Mar.', 'Mié.', 'Jue.', 'Vie.', 'Sáb.', 'Dom.'];
        $datos = [];

        foreach ($diasSemana as $dia) {
            $ventasDia = $ventasPorDia->firstWhere('day', $dia);
            $datos[] = [
                'day' => $dia,
                'sales' => $ventasDia['sales'] ?? 0,
            ];
        }

        return [
            'datos' => $datos,
            'total' => collect($datos)->sum('sales'),
            'max' => collect($datos)->max('sales'),
            'crecimiento' => '+0.0%', // Placeholder
        ];
    }


    private static function calcularCambio($hoy, $ayer): string
    {
        if ($ayer == 0)
            return '+100%';
        $diff = (($hoy - $ayer) / $ayer) * 100;
        $sign = $diff >= 0 ? '+' : '';
        return $sign . number_format($diff, 1) . '%';
    }

    private static function calcularTrend($hoy, $ayer): string
    {
        return $hoy >= $ayer ? 'up' : 'down';
    }
    /**
     * Lista las ventas de un negocio con paginación y filtros opcionales.
     *
     * @param int $negocio_id
     * @param array $filtros ['sucursal_id' => int|null, 'page' => int|null]
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
     */
    public static function listarVentas(int $negocio_id, array $filtros = [])
    {
        $query = Venta::with(['detalles', 'comprobante', 'notas'])
        ->orderBy('fecha_emision','desc')
        ->where('negocio_id', $negocio_id);

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