<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'descripcion_marca',
        'tipo_negocio',
        'categoria_id',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($marca) {
            if (empty($marca->uuid)) {
                $marca->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the category that owns the brand.
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    /**
     * Get the products for the brand.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'marca_id');
    }
}

