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
        Schema::create('sunat_catalogo_7', function (Blueprint $table) {
            $table->string('codigo')->primary(); // Ej: 10, 20, etc.
            $table->string('descripcion');
            $table->string('tipo_afectacion'); // gravado, exonerado, inafecto, exportacion
            $table->boolean('aplica_igv')->default(false);
            $table->decimal('tasa_igv', 5, 2)->default(0.00);
            $table->boolean('es_gratuito')->default(false);
            $table->boolean('considerar_para_operacion')->default(true);
            $table->boolean('afecta_base')->default(true);
            $table->boolean('uso_comun')->default(false);
            $table->text('observacion')->nullable();
            $table->boolean('estado')->default(false); // Activo o no
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sunat_catalogo_7');
    }
};
