<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\Comercial\ClienteServicio;
use App\Services\Facturacion\Sunat\SunatServicio;
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

        // Convertimos la búsqueda en filtros múltiples
        $busqueda = $request->busqueda;

        $clientes = ClienteServicio::listarClientes([
            'numero_documento' => $busqueda,
            'nombre_completo' => $busqueda,
            'nombre_comercial' => $busqueda,
            'telefono' => $busqueda,
        ])->take(20)->get(); // Opcional: limitar resultados

        return response()->json($clientes);
    }

    // Método para registrar un nuevo cliente
    public function registrar(Request $request)
    {
        $request->validate([
            'tipo_cliente_id' => 'required|in:empresa,persona',
            'tipo_documento_id' => 'required',
            'numero_documento' => 'required',
            'nombre_completo' => 'required',
            'nombre_comercial' => 'nullable',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'distrito' => 'nullable|string',
            'provincia' => 'nullable|string',
            'departamento' => 'nullable|string',
            'puntos' => 'nullable|integer|min:0',
            'notas' => 'nullable|string',
        ]);

        try {
            // Datos que serán enviados al servicio
            $data = $request->only([
                'tipo_cliente_id',
                'tipo_documento_id',
                'numero_documento',
                'nombre_completo',
                'nombre_comercial',
                'email',
                'telefono',
                'whatsapp',
                'direccion',
                'distrito',
                'provincia',
                'departamento',
                'puntos',
                'notas',
            ]);

            // El servicio detecta automáticamente el dueño según el usuario autenticado
            $cliente = ClienteServicio::guardar($data); // Este debe retornar el modelo si deseas enviarlo como respuesta

            return response()->json($cliente, 201);
        } catch (\Throwable $th) {
            report($th);
            return response()->json(['error' => 'Error al registrar el cliente'], 500);
        }
    }
    public function sunatPorRuc(Request $request)
    {
        $request->validate([
            'ruc' => 'required|string|size:11', // asumimos solo RUC
        ]);

        try {
            $data = SunatServicio::consultarPorRuc($request->ruc);

            return response()->json([
                'success' => true,
                'data' => [
                    'numero_documento' => $data['ruc'],
                    'nombre_completo' => $data['razonSocial'],
                    'nombre_comercial' => $data['nombreComercial'],
                    'direccion' => $data['direccion'],
                    'departamento' => $data['departamento'],
                    'provincia' => $data['provincia'],
                    'distrito' => $data['distrito'],
                    'telefono' => implode(', ', $data['telefonos'] ?? []),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar SUNAT: ' . $th->getMessage(),
            ], 500);
        }
    }
}
