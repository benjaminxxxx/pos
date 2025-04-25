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
        Schema::create('informaciones_adicionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocio_id')->constrained()->onDelete('cascade');
            $table->string('clave');
            $table->text('valor');
            $table->enum('ubicacion', ['Cabecera', 'Centro', 'Pie']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informaciones_adicionales');
    }
};
