<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    protected $fillable = [
        'uuid',

        'cliente_id',
        'nombre_cliente',
        'documento_cliente',
        'tipo_documento_cliente',

        'estado',
        'modo_venta',

        'valor_venta',
        'sub_total',
        'redondeo',
        'monto_importe_venta',
        

        'monto_operaciones_gravadas',
        'monto_operaciones_exoneradas',
        'monto_operaciones_inafectas',
        'monto_operaciones_exportacion',
        'monto_operaciones_gratuitas',
        'monto_igv',
        'monto_igv_gratuito',
        'icbper',
        'total_impuestos',

        'tipo_comprobante_codigo',
        'serie_comprobante',
        'correlativo_comprobante',

        'sunat_comprobante_pdf',
        'voucher_pdf',
        'sunat_xml_firmado',
        'sunat_cdr',

        'caja_id',
        'sucursal_id',

        'fecha_emision',
        'fecha_pago',
        'negocio_id',
        'tipo_factura'
    ];
    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'negocio_id');
    }
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class)->withDefault();
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class)->withDefault();
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class)->withDefault();
    }
   
}
