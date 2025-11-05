<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeleccionarNegocio
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si no está autenticado, continúa normal
        if (!auth()->check()) {
            return $next($request);
        }

        // Si ya existe negocio seleccionado, continuar
        if (session()->has('negocio_actual_uuid')) {
            return $next($request);
        }

        $user = auth()->user();
        $negocios = $user->negocios ?? [];

        // Si solo tiene un negocio, seleccionar automáticamente
        if ($negocios->count() === 1) {
            session(['negocio_actual_uuid' => $negocios->first()->uuid]);
            return $next($request);
        }

        // Si tiene varios, redirigir a vista de selección
        if ($negocios->count() > 1 && !$request->is('seleccionar-negocio')) {
            return redirect()->route('seleccionar-negocio');
        }
        return $next($request);
    }
}
