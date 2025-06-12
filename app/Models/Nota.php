<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;
    protected $fillable = [
        'tipo_doc',
        'serie_comprobante',
        'correlativo_comprobante',
        'fecha_emision',
        'tip_doc_afectado',
        'num_doc_afectado',
        'cod_motivo',
        'des_motivo',
        'tipo_moneda',
        'mto_oper_gravadas',
        'mto_igv',
        'total_impuestos',
        'mto_imp_venta',
        'cliente_id',
        'empresa_id',
        'forma_pago',
        'cuotas',
        'guias',
        'negocio_id',
        'sucursal_id',
        'venta_id',

        'sunat_comprobante_pdf',
        'voucher_pdf',
        'sunat_xml_firmado',
        'sunat_cdr'
    ];
    public function negocio()
    {
        return $this->belongsTo(Negocio::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
