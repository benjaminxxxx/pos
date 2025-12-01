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
        Schema::table('disenios_disponibles', function (Blueprint $table) {
            $table->integer('base_width_mm')->nullable()->after('height_mm');
            $table->integer('base_height_mm')->nullable()->after('base_width_mm');

            // Orientación base
            $table->string('base_orientation')->nullable()->after('base_height_mm'); // portrait | landscape | auto

            // Altura flexible por defecto
            $table->boolean('base_altura_flexible')->default(false)->after('base_orientation');

            // Márgenes base
            $table->integer('base_margin_top_mm')->default(0)->after('base_altura_flexible');
            $table->integer('base_margin_bottom_mm')->default(0)->after('base_margin_top_mm');
            $table->integer('base_margin_left_mm')->default(0)->after('base_margin_bottom_mm');
            $table->integer('base_margin_right_mm')->default(0)->after('base_margin_left_mm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disenios_disponibles', function (Blueprint $table) {
            $table->dropColumn([
                'base_width_mm',
                'base_height_mm',
                'base_orientation',
                'base_altura_flexible',
                'base_margin_top_mm',
                'base_margin_bottom_mm',
                'base_margin_left_mm',
                'base_margin_right_mm',
            ]);
        });
    }
};
