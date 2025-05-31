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
        Schema::create('unidades', function (Blueprint $table) {
            $table->string('codigo', 5)->primary(); // clave SUNAT: ej. "BJ", "BX", etc.
            $table->string('descripcion');         // descripción oficial SUNAT
            $table->string('alt')->nullable();     // alternativa amigable (opcional)
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};

