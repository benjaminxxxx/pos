<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    protected $table = 'tipos_movimiento';
    protected $fillable = [
        'slug',
        'codigo',
        'nombre',

        'tipo_flujo',
        'es_sistema',
        'activo',
        
        'cuenta_id',
    ];

}
