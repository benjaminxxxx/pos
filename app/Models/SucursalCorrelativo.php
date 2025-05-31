<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SucursalCorrelativo extends Model
{
    use HasFactory;

    protected $table = 'sucursal_correlativo';

    protected $fillable = [
        'sucursal_id',
        'correlativo_id'
    ];
    public function correlativo()
    {
        return $this->belongsTo(Correlativo::class, 'correlativo_id');
    }
}

