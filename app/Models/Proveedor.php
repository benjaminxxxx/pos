<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'uuid',
        'cuenta_id',
        'documento_tipo',
        'documento_numero',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'telefono',
        'email',
        'pagina_web',
        'banco',
        'cuenta_bancaria',
        'cci',
        'estado',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];
    protected static function booted()
    {
        static::creating(function ($proveedor) {
            if (empty($proveedor->uuid)) {
                $proveedor->uuid = (string) Str::uuid();
            }
        });
    }
    /* ======================
     |   RELACIONES
     ====================== */

    // Relación con tipo de documento SUNAT
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumentoSunat::class, 'documento_tipo', 'codigo');
    }

    // Auditoría
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function eliminadoPor()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
