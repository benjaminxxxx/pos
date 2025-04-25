<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'sucursal_id',
        'cantidad',
        'stock_minimo'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
    ];

    /**
     * Get the product that owns the stock.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Get the branch that owns the stock.
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Check if the stock is below the minimum.
     */
    public function bajoCritico()
    {
        return $this->cantidad <= $this->stock_minimo;
    }
}

