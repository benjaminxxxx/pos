<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacturaController extends Controller
{
    public function mostrar($serie, $numero)
    {
        $venta = Venta::where('serie_comprobante', $serie)
            ->where('correlativo_comprobante', $numero)
            ->first();

        // Si no existe, abortar
        if (!$venta) {
            abort(404, 'Comprobante no encontrado');
        }

        $user = Auth::user();

        // Validar que sea el dueño del negocio o un superadmin
        $esDueno = $user->hasRole('dueno_tienda') && $user->id === $venta->negocio->user_id;
        $esSuperAdmin = $user->hasRole('dueno_sistema');

        if (!($esDueno || $esSuperAdmin)) {
            abort(403, 'No tienes permiso para ver esta factura');
        }

        $pdf = $venta->sunat_comprobante_pdf;

        return view('documents.visor.comprobante', ['pdf' => $pdf]);
    }
}
