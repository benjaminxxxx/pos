<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User; // Asegúrate de importar el modelo User si no está en el mismo namespace
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\Cuenta;
use App\Models\DetalleCompra;

class Compra extends Model
{
    // =======================================================
    // 🛡️ ATRIBUTOS
    // =======================================================

    /**
     * The attributes that are mass assignable.
     * Incluye todos los nuevos campos del migrate.
     */
    protected $fillable = [
        // Relaciones
        'cuenta_id',
        'sucursal_id',
        'proveedor_id',

        // Datos del Comprobante
        'tipo_comprobante',
        'numero_comprobante',
        'forma_pago',
        'fecha_comprobante',
        'fecha_vencimiento', // 🆕 Nuevo campo
        'glosa_o_observacion', // 🆕 Nombre corregido de 'observacion'

        // Inmutabilidad del Proveedor
        'proveedor_razon_social', // 🆕 Nuevo campo
        'proveedor_nombre_comercial', // 🆕 Nuevo campo
        'proveedor_documento_numero', // 🆕 Nuevo campo

        // Totales y Control Financiero
        'moneda', // 🆕 Nuevo campo
        'tipo_cambio', // 🆕 Nuevo campo
        'subtotal',
        'igv',
        'total',
        'monto_pagado', // 🆕 Nuevo campo
        'estado_pago', // 🆕 Nuevo campo
        'estado',

        // Auditoría
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'fecha_comprobante' => 'date',
        'fecha_vencimiento' => 'date', // 🆕 Asegurar que se trate como fecha
        'estado' => 'boolean', // 💡 Campo 'estado' suele ser un booleano (true/false)
        'subtotal' => 'decimal:2', // 💡 Sugerencia: Castear para asegurar la precisión
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'tipo_cambio' => 'decimal:4', // 💡 Castear con la precisión definida en la migración
    ];
    
    // 💡 Puedes agregar $timestamps = true; si no está implícito, pero por defecto lo está.

    // =======================================================
    // 🔗 RELACIONES
    // =======================================================

    // 👉 Relación con la cuenta (propietaria del negocio)
    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }

    // 👉 Relación con la sucursal donde se registró la compra
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    // 👉 Relación con el proveedor
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    // 👉 Relación con los detalles de la compra
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    // 👉 Auditoría: Creador del registro
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 👉 Auditoría: Último actualizador del registro
    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}