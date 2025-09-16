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
        Schema::create('disenios_disponibles', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion'); // Ej: "A5", "Ticket térmico"
            $table->string('codigo'); // Ej: "a5", "ticket"
            $table->string('tipo_comprobante_codigo'); // referencia al código de tipo_comprobantes
            $table->string('preview')->nullable(); // URL o ruta de la imagen de previsualización
            $table->boolean('activo')->default(true);
            $table->integer('width_mm')->nullable(); // ancho en mm, opcional
            $table->integer('height_mm')->nullable(); // alto en mm, opcional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disenios_disponibles');
    }
};
