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

}
