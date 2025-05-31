<?php

namespace App\Traits\Sunat;

use App\Models\Unidad;

trait UnidadesTrait
{
    public function getUnidades()
    {
        return Unidad::orderBy('descripcion', 'asc')
            ->get();
    }
    public function getUnidadPreseleccionada(){
        return 'NIU';
    }
}
