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
        Schema::create('tipos_movimiento', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique(); // INMUTABLE
            $table->string('codigo');         // Visible / editable
            $table->string('nombre');

            $table->enum('tipo_flujo', ['ingreso', 'egreso', 'neutro']);

            $table->boolean('es_sistema')->default(false);
            $table->boolean('es_automatico')->default(false);   // no manual
            $table->boolean('activo')->default(true);

            // NULL = GLOBAL
            $table->foreignId('cuenta_id')
                ->nullable()
                ->constrained('cuentas');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['cuenta_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_movimiento');
    }
};
