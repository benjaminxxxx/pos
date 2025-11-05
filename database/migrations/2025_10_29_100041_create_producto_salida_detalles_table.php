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
        Schema::create('producto_salida_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_salida_id')->constrained('producto_salidas')->cascadeOnDelete();
            $table->foreignId('producto_entrada_id')->constrained('producto_entradas')->cascadeOnDelete();

            $table->decimal('cantidad', 12, 3);
            $table->decimal('costo_unitario', 12, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_salida_detalles');
    }
};
