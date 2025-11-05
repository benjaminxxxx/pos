<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoSalidaDetalle extends Model
{
    protected $table = 'producto_salida_detalles';

    protected $fillable = [
        'producto_salida_id',
        'producto_entrada_id',
        'cantidad',
        'costo_unitario',
    ];

    public function salida()
    {
        return $this->belongsTo(ProductoSalida::class, 'producto_salida_id');
    }

    public function entrada()
    {
        return $this->belongsTo(ProductoEntrada::class, 'producto_entrada_id');
    }
}
