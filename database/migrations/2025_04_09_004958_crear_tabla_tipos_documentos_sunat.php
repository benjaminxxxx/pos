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
        Schema::create('tipos_documentos_sunat', function (Blueprint $table) {
            $table->string('codigo', 2)->primary(); // Alfanumérico, ejemplo: '0', '1', 'A', etc.
            $table->string('nombre'); // Nombre completo del tipo de documento.
            $table->string('nombre_corto'); // Nombre corto para mostrar en tablas.
            $table->timestamps(); // Fechas de creación y actualización.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_documentos_sunat');
    }
};
