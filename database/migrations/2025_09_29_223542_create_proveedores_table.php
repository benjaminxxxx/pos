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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            
            $table->uuid()->unique();
            // Relación con la cuenta
            $table->foreignId('cuenta_id')->constrained('cuentas')->cascadeOnDelete();

            // Relación con tipos_documentos_sunat (RUC, DNI, CE, etc.)
            $table->string('documento_tipo', 2);
            $table->foreign('documento_tipo')
                ->references('codigo')
                ->on('tipos_documentos_sunat')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Identificación única dentro de la cuenta
            $table->string('documento_numero', 20);
            $table->unique(['cuenta_id', 'documento_numero']); // evita duplicados en la misma cuenta

            // Datos de identificación
            $table->string('razon_social', 255);
            $table->string('nombre_comercial', 255)->nullable();

            // Datos de contacto
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('pagina_web', 150)->nullable();

            // Datos financieros opcionales
            $table->string('banco', 100)->nullable();
            $table->string('cuenta_bancaria', 50)->nullable();
            $table->string('cci', 30)->nullable();

            // Estado del proveedor
            $table->enum('estado', ['ACTIVO', 'INACTIVO', 'ELIMINADO'])->default('ACTIVO');

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // Timestamps y soft delete
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
