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
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('sunat_estado', 50)->nullable();
            $table->text('sunat_cdr_descripcion')->nullable();
            $table->string('sunat_cdr_codigo', 20)->nullable();
            $table->foreignId('venta_origen_id')->nullable()->constrained('ventas');
            $table->string('serie_origen')->nullable();
            $table->string('correlativo_origen')->nullable();
            $table->text('motivo_regularizacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'sunat_estado',
                'sunat_cdr_descripcion',
                'sunat_cdr_codigo',
                'venta_origen_id',
                'serie_origen',
                'correlativo_origen',
                'motivo_regularizacion'
            ]);
        });
    }
};
