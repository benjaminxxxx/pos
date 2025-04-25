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
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');

            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('nombre_producto');
            $table->string('categoria_producto')->nullable();

            // Relación al catálogo 07

            $table->string('codigo_afectacion_igv', 2)->nullable();
            $table->foreign('codigo_afectacion_igv')
                ->references('codigo')
                ->on('sunat_catalogo_7')
                ->nullOnDelete();

            $table->string('unidad');
            $table->decimal('factor', 8, 2)->default(1);

            $table->decimal('valor_unitario', 10, 2)->default(0);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('valor_gratuito', 10, 2)->nullable();

            $table->decimal('cantidad', 10, 2);
            $table->decimal('subtotal', 10, 2);

            $table->decimal('porcentaje_igv', 5, 2)->default(18.00);
            $table->decimal('igv', 10, 2)->nullable();
            $table->decimal('total_impuestos', 10, 2)->nullable();
            $table->decimal('total', 10, 2);

            $table->boolean('es_gratuita')->default(false);
            $table->boolean('es_icbper')->default(false);

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
