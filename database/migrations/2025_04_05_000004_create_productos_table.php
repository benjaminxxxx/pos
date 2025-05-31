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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('codigo_barra')->nullable()->unique();
            $table->string('sunat_code')->nullable();

            $table->text('descripcion'); // ← antes era 'nombre_producto'
            $table->text('detalle')->nullable(); // ← antes era 'descripcion', para info extendida

            $table->string('imagen_path')->nullable();

            $table->decimal('porcentaje_igv', 5, 2)->nullable(); // ← antes era 'igv'
            $table->decimal('monto_venta', 10, 2);
            $table->decimal('monto_venta_sinigv', 10, 2);
            $table->decimal('monto_compra', 10, 2); 
            $table->decimal('monto_compra_sinigv', 10, 2); 

            $table->string('unidad', 5);

            $table->foreignId('categoria_id')->nullable()->constrained('categorias_productos')->onDelete('set null');
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->onDelete('set null');
            $table->foreignId('negocio_id')->constrained('negocios')->onDelete('cascade');
            $table->foreignId('creado_por')->constrained('users');

            $table->boolean('activo')->default(true);

            $table->string('tipo_afectacion_igv', 2)->nullable(); // ← antes era 'tipo_afectacion_igv'
            $table->foreign('tipo_afectacion_igv')->references('codigo')->on('sunat_catalogo_7')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

