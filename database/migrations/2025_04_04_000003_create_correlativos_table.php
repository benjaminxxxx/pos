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
        Schema::create('correlativos', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('negocio_id')->constrained()->onDelete('cascade'); //recien agregado
            $table->string('tipo_comprobante_codigo')->nullable();
            $table->string('serie', 10);
            $table->integer('correlativo_actual')->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('tipo_comprobante_codigo')->references('codigo')->on('tipo_comprobantes')->onDelete('set null');
        });

        // Tabla pivote para la relación muchos a muchos entre sucursales y correlativos
        Schema::create('sucursal_correlativo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('correlativo_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['sucursal_id', 'correlativo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursal_correlativo');
        Schema::dropIfExists('correlativos');
    }
};

