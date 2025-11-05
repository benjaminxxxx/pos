<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class ProductoSalida extends Model
{
    use HasFactory;

    protected $table = 'producto_salidas';

    protected $fillable = [
        'producto_id',
        'sucursal_id',
        'tipo_salida',
        'cantidad',
        'costo_unitario',
        'fecha_salida',
        'referencia_id',
        'referencia_tipo',
        'created_by',
        'estado',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'cantidad' => 'float',
        'costo_unitario' => 'float',
    ];

    // Relaciones
    public function detalles()
    {
        return $this->hasMany(ProductoSalidaDetalle::class, 'producto_salida_id');
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
    public function costoUnitarioPromedio(): Attribute
    {
        return Attribute::get(function () {
            // Evita errores si no hay detalles
            if ($this->detalles->isEmpty()) {
                return 0;
            }

            $totalCantidad = $this->detalles->sum('cantidad');
            $totalCosto = $this->detalles->sum(fn($d) => $d->cantidad * $d->costo_unitario);

            if ($totalCantidad == 0) {
                return 0;
            }

            return round($totalCosto / $totalCantidad, 2);
        });
    }
}
