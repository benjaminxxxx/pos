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
        Schema::create('categorias_productos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('descripcion');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_productos')->onDelete('set null');
            $table->enum('tipo_negocio', ['ferreteria', 'hotel', 'panaderia', 'libreria', 'polleria','restaurante'])->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_productos');
    }
};

