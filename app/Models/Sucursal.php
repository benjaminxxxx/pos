<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'uuid',
        'negocio_id',
        'nombre',
        'direccion',
        'telefono',
        'email',
        'es_principal',
        'estado',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'estado' => 'boolean',
    ];

    public function getModoVentaAttribute(){
        return $this->negocio->modo;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sucursal) {
            if (empty($sucursal->uuid)) {
                $sucursal->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the negocio that owns the sucursal.
     */
    public function negocio()
    {
        return $this->belongsTo(Negocio::class);
    }

    /**
     * Get the correlativos for the sucursal.
     */
    public function correlativos()
    {
        return $this->belongsToMany(Correlativo::class, 'sucursal_correlativo');
    }

    /**
     * Get the empleados for the sucursal.
     */
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }

    /**
     * Get the ventas for the sucursal.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Get the cajas for the sucursal.
     */
    public function cajas()
    {
        return $this->hasMany(Caja::class);
    }
}

