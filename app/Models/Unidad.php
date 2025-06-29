<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $primaryKey = 'codigo';  // Aquí indicas la PK personalizada
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'unidades';
    protected $fillable = [
        'codigo',
        'descripcion',
        'alt',
        'validado_sunat'
    ];
}
