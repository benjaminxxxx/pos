<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisenioDisponible extends Model
{
    use HasFactory;

    protected $table = 'disenios_disponibles';

    protected $fillable = [
        'descripcion',
        'codigo',
        'tipo_comprobante_codigo',
        'preview',
        'activo',
    ];

    // Relación: un diseño puede estar asignado a varias configuraciones
    public function asignaciones()
    {
        return $this->hasMany(DisenioImpresion::class, 'disenio_id');
    }
}
