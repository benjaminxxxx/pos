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
        Schema::create('producto_entradas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();

            $table->string('tipo_entrada', 30);
            $table->decimal('cantidad', 12, 3);
            $table->decimal('stock_disponible', 12, 3)->default(0);
            $table->decimal('costo_unitario', 12, 4)->default(0);
            $table->date('fecha_ingreso')->index();

            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('referencia_tipo')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_entradas');
    }
};
