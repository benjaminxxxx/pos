<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SunatCatalogo9 extends Model
{
    protected $table = 'sunat_catalogo_9';
    protected $primaryKey = 'codigo';
    public $incrementing = false; // No es autoincremental
    protected $keyType = 'string'; // Tipo string

    protected $fillable = ['codigo', 'descripcion','motivo','requiere_detalle'];
}
