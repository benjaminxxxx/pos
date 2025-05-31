<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Negocio extends Model
{
    use HasFactory;

    protected $table = 'negocios';

    protected $fillable = [
        'user_id',
        'uuid',
        'nombre_legal',
        'nombre_comercial',
        'ruc',
        'direccion',
        'ubigeo',
        'departamento',
        'provincia',
        'distrito',
        'codigo_pais',
        'urbanizacion',
        'tipo_negocio',
        'usuario_sol',
        'clave_sol',
        'client_secret',
        'modo',
        'certificado',
        'logo_factura',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generar un UUID automáticamente al crear un negocio
        static::creating(function ($negocio) {
            $negocio->uuid = Str::uuid();
        });
    }

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con Información Adicional
    public function informacionAdicional()
    {
        return $this->hasMany(InformacionAdicional::class);
    }

    // Relación con Sucursales
    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }
    public function categorias()
    {
        return CategoriaProducto::where('tipo_negocio',$this->tipo_negocio)->get();
    }
}

