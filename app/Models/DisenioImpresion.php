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
