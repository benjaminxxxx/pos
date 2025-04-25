<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    // Definir la tabla si no sigue la convención plural
    protected $table = 'clientes';

    // Definir los campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre_completo',
        'tipo_documento_id',
        'numero_documento',
        'telefono',
        'direccion',
        'departamento',
        'provincia',
        'email',
        'distrito',
        'tipo_cliente_id',
        'nombre_comercial',
        'ruc_facturacion',
        'direccion_facturacion',
        'puntos',
        'notas',
        'dueno_tienda_id', // El ID del dueño de la tienda
    ];

    // Relación con la tabla 'tipos_documentos_sunat' (tipo de documento)
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumentoSunat::class, 'tipo_documento_id', 'codigo');
    }

    // Relación con el usuario (dueño de la tienda)
    public function duenoTienda()
    {
        return $this->belongsTo(User::class, 'dueno_tienda_id');
    }

}
