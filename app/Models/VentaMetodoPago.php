<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaMetodoPago extends Model
{
    protected $table = 'venta_metodo_pagos';

    protected $fillable = [
        'venta_id',         // sale_id
        'metodo',           // method
        'monto',            // amount
    ];

    // Relación con venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
