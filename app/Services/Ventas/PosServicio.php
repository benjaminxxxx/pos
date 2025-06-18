<?php

namespace App\Services\Ventas;
use App\Services\Producto\InventarioServicio;
use Illuminate\Support\Facades\Auth;
use App\Models\Negocio;
use App\Models\Sucursal;

class PosServicio
{
    public static function obtenerEstadisticas($filtro)
    {
        $user = Auth::user();

        // Validación: Filtro general
        if ($filtro === 'general') {
            if (!$user->hasRole('dueno_tienda')) {
                throw new \Exception("No tienes permiso para acceder a la vista general.");
            }
        }

        // Validación: Filtro negocio-{id}
        elseif (str_starts_with($filtro, 'negocio-')) {
            $negocioId = (int) str_replace('negocio-', '', $filtro);

            if (!$user->hasRole('dueno_tienda')) {
                throw new \Exception("Solo un dueño de tienda puede acceder a negocios.");
            }

            $negocio = $user->negocios()->find($negocioId);
            if (!$negocio) {
                throw new \Exception("No tienes acceso al negocio con ID $negocioId.");
            }
        }

        // Validación: Filtro sucursal-{id}
        elseif (str_starts_with($filtro, 'sucursal-')) {
            $sucursalId = (int) str_replace('sucursal-', '', $filtro);

            if ($user->hasRole('dueno_tienda')) {
                $sucursal = Sucursal::whereHas('negocio', function ($q) use ($user) {
                    $q->whereIn('id', $user->negocios->pluck('id'));
                })->find($sucursalId);

                if (!$sucursal) {
                    throw new \Exception("No tienes acceso a la sucursal con ID $sucursalId.");
                }
            } elseif ($user->hasRole('vendedor')) {
                if (!$user->sucursal || $user->sucursal->id !== $sucursalId) {
                    throw new \Exception("No tienes permiso para acceder a esta sucursal.");
                }
            } else {
                throw new \Exception("No tienes un rol válido para acceder a esta sucursal.");
            }
        }

        // Validación: Filtro no reconocido
        else {
            throw new \Exception("Filtro inválido o no reconocido.");
        }

        // Si pasó todas las validaciones, obtenemos los datos
        $data = [];
        $data['tarjetas_estadisticas'] = InformacionVenta::obtenerTarjetasEstadisticas($filtro);
        $data['ventas_semanales'] =  InformacionVenta::obtenerVentasSemanales($filtro);
        $data['alertas_inventario'] =  InventarioServicio::obtenerAlertasInventario($filtro);
        
        // Aquí podrías agregar más datos con otras claves, ejemplo:
        // $data['top_productos'] = InformacionVenta::obtenerTopProductos($filtro);

        return $data;
    }
    public static function obtenerFiltros($user)
    {
        $filtros = [];

        if ($user->hasRole('dueno_tienda')) {
            $filtros[] = ['value' => 'general', 'label' => 'VISTA GENERAL'];

            foreach ($user->negocios as $negocio) {
                $filtros[] = [
                    'value' => 'negocio-' . $negocio->id,
                    'label' => mb_strtoupper($negocio->nombre_legal)
                ];

                foreach ($negocio->sucursales as $sucursal) {
                    $filtros[] = [
                        'value' => 'sucursal-' . $sucursal->id,
                        'label' => '└ ' . mb_strtoupper($sucursal->nombre)
                    ];
                }
            }
        } elseif ($user->hasRole('vendedor') && $user->sucursal) {
            $filtros[] = [
                'value' => 'sucursal-' . $user->sucursal->id,
                'label' => mb_strtoupper($user->sucursal->nombre)
            ];
        }

        return $filtros;
    }
    
}
