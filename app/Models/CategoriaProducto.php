<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoriaProducto extends Model
{
    use HasFactory;

    protected $table = 'categorias_productos';

    protected $fillable = [
        'uuid',
        'descripcion',
        'categoria_id',
        'tipo_negocio',
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

        static::creating(function ($categoria) {
            if (empty($categoria->uuid)) {
                $categoria->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the parent category.
     */
    public function categoriaPadre()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    /**
     * Get the subcategories.
     */
    public function subcategorias()
    {
        return $this->hasMany(CategoriaProducto::class, 'categoria_id');
    }

    /**
     * Get the products for the category.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    /**
     * Get the services for the category.
     */
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'categoria_id');
    }

    /**
     * Get the brands for the category.
     */
    public function marcas()
    {
        return $this->hasMany(Marca::class, 'categoria_id');
    }
}

