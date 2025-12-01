<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisenioImpresion extends Model
{
    use HasFactory;

    protected $table = 'disenios_impresion';

    protected $fillable = [
        'negocio_id',
        'sucursal_id',
        'disenio_id',
        'activo',

        'custom_width_mm',
        'custom_height_mm',
        'custom_altura_flexible',
        'custom_orientation',
        'custom_margin_top_mm',
        'custom_margin_bottom_mm',
        'custom_margin_left_mm',
        'custom_margin_right_mm',
    ];

    public function disenioDisponible()
    {
        return $this->belongsTo(DisenioDisponible::class, 'disenio_id');
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocio_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
