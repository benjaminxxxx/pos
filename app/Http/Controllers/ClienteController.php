<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    // Método para buscar clientes
    public function buscar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'busqueda' => 'required|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Si pasa la validación, buscamos los clientes
        $clientes = Cliente::where('nombre_completo', 'like', '%' . $request->busqueda . '%')
            ->orWhere('numero_documento', 'like', '%' . $request->busqueda . '%')
            ->orWhere('nombre_comercial', 'like', '%' . $request->busqueda . '%')
            ->get();

        return response()->json($clientes);
    }

    // Método para registrar un nuevo cliente
    public function registrar(Request $request)
    {

        // Validamos los datos del cliente
        $request->validate([
            'tipo_cliente_id' => 'required|in:empresa,persona',
            'numero_documento' => 'required|unique:clientes,numero_documento',
            'tipo_documento_id' => 'required|exists:tipos_documentos_sunat,codigo',
            'nombre_completo' => 'required_if:tipo_cliente_id,persona',
            'nombre_comercial' => 'required_if:tipo_cliente_id,empresa',
        ]);

        $user = Auth::user(); // Usuario autenticado

        if ($user->hasRole('dueno_tienda')) {
            $duenoTiendaId = $user->id;
        } elseif ($user->hasRole('vendedor')) {
            // Suponiendo que el vendedor tiene una relación belongsTo hacia el dueño de la tienda
            $duenoTiendaId = $user->dueno_tienda_id; // funcion por crear aun no existe
        } else {
            return response()->json(['error' => 'Usuario no autorizado para registrar clientes.'], 403);
        }

        // Creamos un nuevo cliente
        $cliente = new Cliente();
        $cliente->tipo_cliente_id = $request->tipo_cliente_id;
        $cliente->numero_documento = $request->numero_documento;
        $cliente->tipo_documento_id = $request->tipo_documento_id;
        $cliente->dueno_tienda_id = $duenoTiendaId;


        // Asignamos nombre completo o nombre comercial según el tipo de cliente
        if ($request->tipo_cliente_id == 'persona') {
            $cliente->nombre_completo = $request->nombre_completo;
        } else {
            $cliente->nombre_comercial = $request->nombre_comercial;
        }

        // Guardamos el cliente en la base de datos
        $cliente->save();

        // Retornamos el cliente recién creado
        return response()->json($cliente, 201);
    }
}
