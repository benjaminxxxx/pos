<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacionAdicional extends Model
{
    use HasFactory;

    protected $table = 'informaciones_adicionales';

    protected $fillable = [
        'negocio_id',
        'clave',
        'valor',
        'ubicacion',
    ];

    // Relación con Negocio
    public function negocio()
    {
        return $this->belongsTo(Negocio::class);
    }
}
