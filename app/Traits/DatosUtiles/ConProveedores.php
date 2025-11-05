<?php

namespace App\Traits\DatosUtiles;
use App\Models\Proveedor;

trait ConProveedores
{
    public $proveedores;
    public function bootConProveedores()
    {
        $this->cargarProveedores();
    }
    public function cargarProveedores()
    {
        $this->proveedores = Proveedor::where('cuenta_id', auth()->user()->cuenta_id)
            ->orderBy('nombre_comercial')
            ->get();
    }
}