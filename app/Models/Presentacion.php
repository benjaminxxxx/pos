<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    use HasFactory;

    protected $table = 'presentaciones';

    protected $fillable = [
        'producto_id',
        'codigo_barra',
        'unidad',
        'descripcion',
        'factor',
        'precio',
        'activo'
    ];

    protected $casts = [
        'factor' => 'decimal:2',
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Get the product that owns the presentation.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

}

