<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            $table->string('nombre_producto');
            $table->text('descripcion')->nullable();
            $table->string('imagen_path')->nullable();
            $table->decimal('igv', 5, 2)->nullable();
            $table->decimal('precio_base', 10, 2);
            $table->decimal('precio_compra', 10, 2);
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_productos')->onDelete('set null');
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->onDelete('set null');
            $table->foreignId('negocio_id')->constrained('negocios')->onDelete('cascade');
            $table->foreignId('creado_por')->constrained('users');
            $table->boolean('activo')->default(true);
            $table->enum('tipo_afectacion_igv',['gravada','exonerada','inafecta','exportacion','gratuita'])->default('gravada');
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

