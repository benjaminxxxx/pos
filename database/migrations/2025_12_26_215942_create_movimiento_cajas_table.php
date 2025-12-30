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
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_movimiento_id')
                ->constrained('tipos_movimiento');

            $table->foreignId('cuenta_id')
                ->constrained('cuentas');

            $table->foreignId('sucursal_id')
                ->constrained('sucursales');

            $table->foreignId('usuario_id')
                ->constrained('users');

            $table->decimal('monto', 14, 2);
            $table->string('metodo_pago')->nullable();

            $table->string('referencia_tipo')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();

            $table->text('observacion')->nullable();
            $table->timestamp('fecha');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
