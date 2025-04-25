<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';

    protected $fillable = [
        'uuid',
        'nombre',
        'abreviatura',
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

        static::creating(function ($unidad) {
            if (empty($unidad->uuid)) {
                $unidad->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the presentations for the unit.
     */
    public function presentaciones()
    {
        return $this->hasMany(Presentacion::class, 'unidad_id');
    }
}

