<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumentoSunat extends Model
{
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    // Definir el nombre de la tabla si no sigue la convención plural
    protected $table = 'tipos_documentos_sunat';

    // Definir los campos que se pueden llenar masivamente
    protected $fillable = ['codigo', 'nombre', 'nombre_corto'];
}
