<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';
    protected $fillable = [
        'tipo_movimiento_id',
        'cuenta_id',
        'sucursal_id',

        'usuario_id',
        'monto',
        'metodo_pago',

        'referencia_tipo',
        'referencia_id',
        'observacion',
        'fecha',
    ];
    public function tipoMovimiento(){
        return $this->belongsTo(TipoMovimiento::class,'tipo_movimiento_id');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class,'sucursal_id');
    }
    public function usuario(){
        return $this->belongsTo(User::class,'usuario_id');
    }
}
