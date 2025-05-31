<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'codigo_barra',
        'sunat_code',

        'descripcion',    // ← antes era nombre_producto
        'detalle',        // ← antes era descripcion
        'imagen_path',

        'porcentaje_igv', // ← antes era igv
        'monto_venta',
        'monto_venta_sinigv',
        'monto_compra',
        'monto_compra_sinigv',

        'unidad',

        'categoria_id',
        'marca_id',
        'negocio_id',
        'creado_por',

        'activo',
        'tipo_afectacion_igv',
    ];
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            if (empty($producto->uuid)) {
                $producto->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    /**
     * Get the brand that owns the product.
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    /**
     * Get the business that owns the product.
     */
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocio_id');
    }

    /**
     * Get the user that created the product.
     */
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Get the presentations for the product.
     */
    public function presentaciones()
    {
        return $this->hasMany(Presentacion::class, 'producto_id');
    }

    /**
     * Get the stocks for the product.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'producto_id');
    }

    /**
     * Check if the product has sales.
     */
    public function tieneVentas()
    {
        // Implementar lógica para verificar si el producto tiene ventas
        return false;
    }

    /**
     * Get stock for a specific branch.
     */
    public function stockEnSucursal($sucursalId)
    {
        return $this->stocks()->where('sucursal_id', $sucursalId)->first();
    }
}

