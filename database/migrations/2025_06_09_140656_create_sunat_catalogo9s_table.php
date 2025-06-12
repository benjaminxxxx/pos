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
        Schema::create('sunat_catalogo_9', function (Blueprint $table) {
            $table->string('codigo', 2)->primary();
            $table->string('descripcion');
            $table->text('motivo')->nullable();
            $table->boolean('requiere_detalle')->default(false);
            $table->boolean('estado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sunat_catalogo_9');
    }
};
