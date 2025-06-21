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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo')->nullable();
            $table->string('tipo_documento_id', 2); // Permite letras y números
            $table->string('numero_documento')->nullable();
            $table->string('telefono')->nullable(); // Cambio 'telefono_movil' a 'telefono'
            $table->string('direccion')->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('distrito')->nullable(); // Suponiendo que es un código
            $table->enum('tipo_cliente_id',['empresa','persona']);
            $table->string('nombre_comercial')->nullable();
            $table->integer('puntos')->default(0);
            $table->text('notas')->nullable();
            $table->foreign('tipo_documento_id')->references('codigo')->on('tipos_documentos_sunat');
            $table->foreignId('dueno_tienda_id')->constrained('users'); // Relación con el dueño de la tienda (usuario)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
