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

        'base_width_mm',
        'base_height_mm',
        'base_orientation',
        'base_altura_flexible',
        'base_margin_top_mm',
        'base_margin_bottom_mm',
        'base_margin_left_mm',
        'base_margin_right_mm',
    ];

    // Relación: un diseño puede estar asignado a varias configuraciones
    public function asignaciones()
    {
        return $this->hasMany(DisenioImpresion::class, 'disenio_id');
    }
}
