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
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->decimal('compra_monto', 12, 2)->nullable()->after('monto_precio_unitario');
            $table->decimal('compra_igv', 12, 2)->nullable()->after('compra_monto');
            $table->decimal('compra_monto_igv', 12, 2)->nullable()->after('compra_igv');
            $table->decimal('utilidad_neta', 12, 2)->nullable()->after('compra_monto_igv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn(['compra_monto', 'compra_igv', 'compra_monto_igv', 'utilidad_neta']);
        });
    }
};
