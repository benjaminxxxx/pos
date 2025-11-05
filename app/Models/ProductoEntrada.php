<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductoEntrada extends Model
{
    use HasFactory;

    protected $table = 'producto_entradas';

    protected $fillable = [
        'producto_id',
        'sucursal_id',
        'tipo_entrada',
        'cantidad',
        'stock_disponible',
        'costo_unitario',
        'fecha_ingreso',
        'referencia_id',
        'referencia_tipo',
        'created_by',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'cantidad' => 'float',
        'costo_unitario' => 'float',
    ];

    // Relaciones
    public function salidasDetalle()
    {
        return $this->hasMany(ProductoSalidaDetalle::class, 'producto_entrada_id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    protected static function booted()
    {
        static::created(function ($entrada) {
            // 1️⃣ Registrar stock disponible inicial
            $entrada->update(['stock_disponible' => $entrada->cantidad]);

            // 2️⃣ Buscar o crear el stock de la sucursal correspondiente
            $stock = Stock::firstOrCreate(
                [
                    'producto_id' => $entrada->producto_id,
                    'sucursal_id' => $entrada->sucursal_id,
                ],
                [
                    'cantidad' => 0,
                    'stock_minimo' => 0,
                ]
            );

            // 3️⃣ Incrementar el stock total
            $stock->increment('cantidad', $entrada->cantidad);
        });

        static::deleted(function ($entrada) {
            // 1️⃣ Buscar el stock de esa sucursal
            $stock = Stock::where('producto_id', $entrada->producto_id)
                ->where('sucursal_id', $entrada->sucursal_id)
                ->first();

            if ($stock) {
                // 2️⃣ Restar el stock disponible de esa entrada
                $stock->decrement('cantidad', $entrada->stock_disponible);
            }
        });
    }

}
