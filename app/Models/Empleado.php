<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';

    protected $fillable = [
        'uuid',
        'user_id',
        'sucursal_id',
        'dni',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'fecha_contratacion',
        'salario',
        'estado',
    ];


    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($empleado) {
            if (empty($empleado->uuid)) {
                $empleado->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the empleado.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sucursal that owns the empleado.
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Get the ventas for the empleado.
     */
    /*public function ventas()
    {
        return $this->hasMany(Venta::class);
    }*/

    /**
     * Get the cajas for the empleado.
     */
    public function cajas()
    {
        return $this->hasMany(Caja::class, 'responsable_id');
    }

    /**
     * Scope a query to only include active empleados.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope a query to only include empleados from a specific sucursal.
     */
    public function scopeDeSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }
}

