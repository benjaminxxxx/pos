<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SunatCatalogo7 extends Model
{
    protected $table = 'sunat_catalogo_7';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo_afectacion',
        'aplica_igv',
        'tasa_igv',
        'es_gratuito',
        'considerar_para_operacion',
        'afecta_base',
        'uso_comun',
        'observacion',
        'estado',
    ];
}
