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
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->decimal('precio_mayorista', 10, 2)->nullable()->after('precio');
            $table->integer('minimo_mayorista')->nullable()->after('precio_mayorista');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presentaciones', function (Blueprint $table) {
             $table->dropColumn(['precio_mayorista', 'minimo_mayorista']);
        });
    }
};
