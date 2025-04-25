<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    protected $fillable = [
        'venta_id',

        'producto_id',
        'nombre_producto',
        'categoria_producto',

        'unidad',
        'factor',

        'precio_unitario',
        'cantidad',
        'subtotal',
        
        'porcentaje_igv',
        'total_impuestos',
        'igv',
        'total',
        'tipo_afectacion_igv',
        'es_gratuita',
        'es_icbper'
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class)->withDefault();
    }
}
