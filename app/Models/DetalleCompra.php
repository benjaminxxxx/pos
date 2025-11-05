<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCompra extends Model
{
    // =======================================================
    // 🛡️ ATRIBUTOS
    // =======================================================

    /**
     * The attributes that are mass assignable.
     * Incluye todos los nuevos campos para inmutabilidad y cálculo detallado.
     */
    protected $fillable = [
        // Relaciones
        'compra_id',
        'producto_id',

        // Inmutabilidad del Producto
        'producto_nombre', // 🆕 Nuevo: Nombre del producto al momento de la compra
        'producto_sku',    // 🆕 Nuevo: SKU del producto al momento de la compra

        // Cantidades y Unidad
        'cantidad',
        'unidad_medida', // 🆕 Corregido: Nombre de 'unidad'
        'factor_conversion', // 🆕 Corregido: Nombre de 'factor'

        // Precios, Descuentos e Impuestos
        'costo_unitario', // Precio base
        'descuento_porcentaje', // 🆕 Nuevo
        'descuento_monto',      // 🆕 Nuevo
        'subtotal_neto',        // 🆕 Nuevo: Subtotal antes de impuestos (Neto)

        'tipo_igv',        // 🆕 Nuevo: Tipo de afectación del impuesto
        'porcentaje_igv',  // 🆕 Nuevo: Porcentaje aplicado
        'monto_igv',       // 🆕 Nuevo: Monto del impuesto

        // Totales y Auditoría
        'total_linea', // 🆕 Corregido: Nombre de 'total'
        'created_by',  // 🆕 Añadidos
        'updated_by',  // 🆕 Añadidos
    ];

    /**
     * The attributes that should be cast to native types.
     * Se aumenta la precisión de muchos campos a 4 o 6 decimales.
     */
    protected $casts = [
        'cantidad' => 'decimal:4',          // ⬆️ Aumentar precisión para inventario
        'factor_conversion' => 'decimal:6', // ⬆️ Aumentar precisión para conversión
        'costo_unitario' => 'decimal:4',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:4',
        'subtotal_neto' => 'decimal:4',
        'porcentaje_igv' => 'decimal:2',
        'monto_igv' => 'decimal:4',
        'total_linea' => 'decimal:4',
    ];

    // =======================================================
    // 🔗 RELACIONES
    // =======================================================

    // 👉 Pertenece a una compra
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    // 👉 Producto vinculado
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
    
    // 🆕 Auditoría
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}