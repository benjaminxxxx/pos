<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumentoSunat extends Model
{
    use HasFactory;

    // Definir el nombre de la tabla si no sigue la convención plural
    protected $table = 'tipos_documentos_sunat';

    // Definir los campos que se pueden llenar masivamente
    protected $fillable = ['codigo', 'nombre', 'nombre_corto'];
}
