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
         Schema::table('ventas', function (Blueprint $table) {
            $table->string('cliente_ubigueo', 10)->nullable()->after('documento_cliente');
            $table->string('cliente_departamento', 100)->nullable()->after('cliente_ubigueo');
            $table->string('cliente_provincia', 100)->nullable()->after('cliente_departamento');
            $table->string('cliente_distrito', 100)->nullable()->after('cliente_provincia');
            $table->string('cliente_urbanizacion', 150)->nullable()->after('cliente_distrito');
            $table->string('cliente_direccion', 255)->nullable()->after('cliente_urbanizacion');
            $table->string('cliente_cod_local', 20)->nullable()->default('0000')->after('cliente_direccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'cliente_ubigueo',
                'cliente_departamento',
                'cliente_provincia',
                'cliente_distrito',
                'cliente_urbanizacion',
                'cliente_direccion',
                'cliente_cod_local',
            ]);
        });
    }
};
