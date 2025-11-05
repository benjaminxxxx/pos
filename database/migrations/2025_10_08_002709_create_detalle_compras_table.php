<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();

            // =======================================================
            // 1. Relaciones principales
            // =======================================================
            $table->foreignId('compra_id')
                ->constrained('compras')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete(); // Usar nullOnDelete es mejor si el producto se elimina (Soft Delete)

            // =======================================================
            // 2. Inmutabilidad Histórica del Producto
            // Se almacenan los datos del producto al momento de la compra
            // =======================================================
            $table->string('producto_nombre', 255); // Nombre del producto al momento de la compra (inmutable)
            $table->string('producto_sku', 50)->nullable(); // SKU/Código del producto (inmutable)

            // =======================================================
            // 3. Cantidades y Unidad
            // =======================================================
            $table->decimal('cantidad', 12, 4); // Aumentar precisión a 4 decimales para inventario
            $table->string('unidad_medida', 50); // Usar un nombre más claro: unidad_medida
            $table->decimal('factor_conversion', 12, 6)->default(1); // Factor para conversión de unidad (mayor precisión)

            // =======================================================
            // 4. Precios, Descuentos e Impuestos (Detallado y Pre-Impuesto)
            // Esto es crucial para la auditoría y cálculo de costos
            // =======================================================

            // Costo Unitario BASE (sin descuentos ni impuestos)
            $table->decimal('costo_unitario', 12, 4); // El costo real por unidad comprado

            // Descuento
            $table->decimal('descuento_porcentaje', 5, 2)->default(0); // 5% para 99.99%
            $table->decimal('descuento_monto', 12, 4)->default(0); // Monto del descuento aplicado a la línea

            // Subtotal antes de Impuestos (Costo x Cantidad - Descuento)
            $table->decimal('subtotal_neto', 12, 4); // El subtotal de la línea antes de impuestos

            // Impuestos
            $table->string('tipo_igv', 50)->nullable(); // Tipo de IGV (Gravado, Exonerado, Inafecto, etc.)
            $table->decimal('porcentaje_igv', 5, 2)->default(0); // Porcentaje de IGV aplicado (ej: 18.00)
            $table->decimal('monto_igv', 12, 4)->default(0); // Monto calculado del impuesto para la línea

            // =======================================================
            // 5. Totales
            // =======================================================

            $table->decimal('total_linea', 12, 4); // Total de la línea (Neto + IGV)

            // =======================================================
            // 6. Auditoría
            // =======================================================
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Índice para búsquedas rápidas por producto dentro de una compra
            $table->index(['compra_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
