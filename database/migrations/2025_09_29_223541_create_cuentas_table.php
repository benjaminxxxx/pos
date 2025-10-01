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
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();

            // Dueño principal de la cuenta
            $table->foreignId('dueno_id')->constrained('users')->cascadeOnDelete();

            // Datos básicos de la cuenta
            $table->string('nombre');
            $table->enum('estado', ['ACTIVO', 'INACTIVO', 'SUSPENDIDO'])->default('ACTIVO');

            // Datos de suscripción / plan
            $table->enum('plan', ['GRATUITO','MENSUAL','ANUAL'])->default('GRATUITO');
            $table->enum('estado_pago', ['AL_DIA','DEUDA','VENCIDO'])->default('AL_DIA');
            $table->enum('metodo_pago', ['TARJETA','TRANSFERENCIA','PAYPAL','OTRO'])->nullable();
            $table->string('numero_tarjeta', 20)->nullable(); // Últimos 4 dígitos o masked
            $table->date('fecha_inicio_plan')->nullable();
            $table->date('fecha_vencimiento_plan')->nullable();
            $table->decimal('costo_plan', 10, 2)->nullable();
            $table->json('configuracion_pago_json')->nullable(); // Token, IDs de suscripción, etc.

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
