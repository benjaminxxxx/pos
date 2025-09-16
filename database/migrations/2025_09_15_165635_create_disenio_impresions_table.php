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
        Schema::create('disenios_impresion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('negocio_id')->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->unsignedBigInteger('disenio_id');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('disenio_id')
                  ->references('id')
                  ->on('disenios_disponibles')
                  ->cascadeOnDelete();

            // Si ya tienes las tablas negocios y sucursales:
            $table->foreign('negocio_id')->references('id')->on('negocios')->cascadeOnDelete();
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disenios_impresion');
    }
};
