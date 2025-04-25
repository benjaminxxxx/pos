<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'codigo',
        'nombre_servicio',
        'descripcion',
        'precio',
        'igv',
        'categoria_id',
        'sucursal_id',
        'negocio_id',
        'creado_por',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'igv' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($servicio) {
            if (empty($servicio->uuid)) {
                $servicio->uuid = Str::uuid();
            }
            if (empty($servicio->codigo)) {
                $servicio->codigo = 'SRV-' . Str::random(8);
            }
        });
    }

    /**
     * Get the category that owns the service.
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    /**
     * Get the branch that owns the service.
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * Get the business that owns the service.
     */
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocio_id');
    }

    /**
     * Get the user that created the service.
     */
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}

