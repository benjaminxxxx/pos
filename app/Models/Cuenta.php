<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Negocio;
use App\Models\Proveedor;

class Cuenta extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'dueno_id',
        'nombre',
        'estado',

        // Suscripción / plan
        'plan',
        'estado_pago',
        'metodo_pago',
        'numero_tarjeta',
        'fecha_inicio_plan',
        'fecha_vencimiento_plan',
        'costo_plan',
        'configuracion_pago_json',
    ];

    /**
     * Dueño principal de la cuenta
     */
    public function dueno()
    {
        return $this->belongsTo(User::class, 'dueno_id');
    }
    
    /**
     * Negocios asociados a esta cuenta
     */
    public function negocios()
    {
        return $this->hasMany(Negocio::class);
    }

    /**
     * Proveedores asociados a esta cuenta
     */
    public function proveedores()
    {
        return $this->hasMany(Proveedor::class);
    }
}
