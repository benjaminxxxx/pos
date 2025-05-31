<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    protected $fillable = [
        'venta_id',
        'producto_id',
        'unidad',
        'descripcion',
        'cantidad',

        'monto_valor_unitario',
        'monto_valor_gratuito',
        'monto_valor_venta',
        'monto_base_igv',
        'monto_precio_unitario',

        'porcentaje_igv',
        'igv',
        'tipo_afectacion_igv',
        'total_impuestos',

        'categoria_producto',
        'factor',
        'es_gratuita',
        'es_icbper',
        'icbper',
        'factor_icbper'
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
