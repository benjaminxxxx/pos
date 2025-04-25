<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Caja extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla
    protected $table = 'cajas';

    // Campos que se pueden llenar de forma masiva
    protected $fillable = [
        'uuid',
        'user_id',
        'sucursal_id',
        'monto_inicial',
        'monto_cierre',
        'diferencia',
        'estado',
        'abierto_en',
        'cerrada_en',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generar un UUID automáticamente al crear un usuario
        static::creating(function ($caja) {
            $caja->uuid = Str::uuid();
        });
    }

    // Relación: una caja pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: una caja pertenece a una sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
