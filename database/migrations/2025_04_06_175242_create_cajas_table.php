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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // UUID único para identificar la caja
            $table->foreignId('user_id')->constrained('users'); // Usuario que abre la caja
            $table->foreignId('sucursal_id')->constrained('sucursales'); // Relación con sucursales
            $table->decimal('monto_inicial', 10, 2); // Monto con el que inicia la caja
            $table->decimal('monto_cierre', 10, 2)->nullable(); // Monto al cerrar
            $table->decimal('diferencia', 10, 2)->nullable(); // Diferencia entre ingresos y cierre
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->timestamp('abierto_en')->nullable(); // Fecha y hora de apertura
            $table->timestamp('cerrada_en')->nullable(); // Fecha y hora de cierre
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
