<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeleccionarNegocioController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $negocios = $user->negocios;
        return view('auth.seleccionar-negocio', compact('negocios'));
    }

    public function store(Request $request)
    {
        $request->validate(['negocio_uuid' => 'required|exists:negocios,uuid']);

        session(['negocio_actual_uuid' => $request->negocio_uuid]);

        return redirect()->intended('/dashboard'); // o la ruta inicial del sistema
    }
}
