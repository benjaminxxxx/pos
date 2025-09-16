<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('negocios', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre_legal')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('ruc', 11)->nullable();
            $table->string('direccion')->nullable();
            $table->string('usuario_sol')->nullable();
            $table->string('clave_sol')->nullable();
            $table->string('client_secret')->nullable();

            // Ubicación
            $table->string('ubigeo', 6)->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();
            $table->string('codigo_pais', 2)->default('PE');
            $table->string('urbanizacion')->nullable();

            $table->enum('modo', ['desarrollo', 'produccion'])->default('desarrollo');
            $table->text('certificado')->nullable();
            $table->text('logo_factura')->nullable();
            $table->enum('tipo_negocio', ['ferreteria', 'hotel', 'panaderia', 'libreria', 'polleria', 'restaurante','ganaderia'])
                ->default('ferreteria');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negocios');
    }
};
