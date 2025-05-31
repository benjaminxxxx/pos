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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();

            // Campos exactamente como los usa la función getDetallesProductos()
            $table->string('unidad', 5); // → 'unidad'
            $table->string('descripcion'); // → 'descripcion'
            $table->decimal('cantidad', 10, 2); // → 'cantidad'

            $table->decimal('monto_valor_unitario', 10, 2)->default(0); // → 'mtoValorUnitario'
            $table->decimal('monto_valor_gratuito', 10, 2)->nullable(); // → 'mtoValorGratuito'
            $table->decimal('monto_valor_venta', 10, 2); // → 'mtoValorVenta'
            $table->decimal('monto_base_igv', 10, 2); // → 'mtoBaseIgv'
            $table->decimal('porcentaje_igv', 5, 2)->default(18.00); // → 'porcentajeIgv'
            $table->decimal('igv', 10, 2)->nullable(); // → 'igv'

            $table->string('tipo_afectacion_igv', 2)->nullable(); // → 'tipAfeIgv'
            $table->foreign('tipo_afectacion_igv')
                ->references('codigo')->on('sunat_catalogo_7')->nullOnDelete();

            $table->decimal('total_impuestos', 10, 2)->nullable(); // → 'totalImpuestos'
            $table->decimal('monto_precio_unitario', 10, 2); // → 'mtoPrecioUnitario'

            // Campos no usados directamente en la función (los puedes eliminar o conservar)
            $table->string('categoria_producto')->nullable(); // no usado
            $table->decimal('factor', 8, 2)->default(1); // no usado
            $table->boolean('es_gratuita')->default(false); // no usado
            $table->boolean('es_icbper')->default(false); // no usado
            $table->decimal('icbper', 10, 2)->nullable(); // NUEVO: monto total de ICBPER
            $table->decimal('factor_icbper', 8, 2)->nullable(); // NUEVO: factor unitario actual usado

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
