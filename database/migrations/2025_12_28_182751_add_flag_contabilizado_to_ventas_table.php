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
            $table->boolean('flag_contabilizado')
                ->default(false)
                ->after('updated_at')
                ->comment('Control temporal para ventas ya procesadas contablemente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
           $table->dropColumn('flag_contabilizado');
        });
    }
};
