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
        Schema::table('disenios_impresion', function (Blueprint $table) {
             $table->integer('custom_width_mm')->nullable()->after('activo');
            $table->integer('custom_height_mm')->nullable()->after('custom_width_mm');

            $table->boolean('custom_altura_flexible')->nullable()->after('custom_height_mm');

            $table->string('custom_orientation')->nullable()->after('custom_altura_flexible'); // portrait | landscape | auto

            // Márgenes personalizados
            $table->integer('custom_margin_top_mm')->nullable()->after('custom_orientation');
            $table->integer('custom_margin_bottom_mm')->nullable()->after('custom_margin_top_mm');
            $table->integer('custom_margin_left_mm')->nullable()->after('custom_margin_bottom_mm');
            $table->integer('custom_margin_right_mm')->nullable()->after('custom_margin_left_mm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disenios_impresion', function (Blueprint $table) {
             $table->dropColumn([
                'custom_width_mm',
                'custom_height_mm',
                'custom_altura_flexible',
                'custom_orientation',
                'custom_margin_top_mm',
                'custom_margin_bottom_mm',
                'custom_margin_left_mm',
                'custom_margin_right_mm',
            ]);
        });
    }
};
