<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoComprobante extends Model
{

    protected $table = 'tipo_comprobantes';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'estado',
    ];
    
    public function correlativos()
    {
        return $this->hasMany(Correlativo::class, 'tipo_comprobante_codigo','codigo');
    }
}

