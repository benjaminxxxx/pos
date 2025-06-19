<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correlativo extends Model
{
    use HasFactory;

    protected $table = 'correlativos';

    protected $fillable = [
        'negocio_id',
        'tipo_comprobante_codigo',
        'serie',
        'correlativo_actual',
        'estado',
    ];

    protected $casts = [
        'correlativo_actual' => 'integer',
        'estado' => 'boolean',
    ];

    /**
     * Get the tipo_comprobante_codigo that owns the correlativo.
     */
    public function tipoComprobante()
    {
        return $this->belongsTo(TipoComprobante::class,'tipo_comprobante_codigo','codigo');
    }

    /**
     * Get the sucursales for the correlativo.
     */
    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'sucursal_correlativo');
    }
    public function negocio()
    {
        return $this->belongsTo(Negocio::class, 'negocio_id');
    }

    /**
     * Incrementar el correlativo actual y devolver el nuevo valor.
     */
    public function incrementarCorrelativo()
    {
        $this->correlativo_actual += 1;
        $this->save();
        
        return $this->correlativo_actual;
    }

    /**
     * Obtener el siguiente número de comprobante formateado.
     */
    public function obtenerSiguienteNumero($longitud = 8)
    {
        return $this->serie . '-' . str_pad($this->correlativo_actual + 1, $longitud, '0', STR_PAD_LEFT);
    }
}

