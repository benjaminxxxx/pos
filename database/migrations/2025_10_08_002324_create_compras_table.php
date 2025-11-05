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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();

            // =======================================================
            // 1. Relaciones principales
            // =======================================================
            $table->foreignId('cuenta_id')
                ->constrained('cuentas')
                ->cascadeOnDelete();

            $table->foreignId('proveedor_id')
                ->nullable()
                ->constrained('proveedores')
                ->nullOnDelete(); // Mantiene la integridad si se usa Soft Delete

            $table->foreignId('sucursal_id')
                ->constrained('sucursales')
                ->cascadeOnDelete();

            // =======================================================
            // 2. Datos del comprobante
            // =======================================================
            $table->string('tipo_comprobante', 50); // ej: FACTURA, BOLETA, etc.
            $table->string('numero_comprobante', 50);
            $table->string('forma_pago', 30)->default('CONTADO'); // o CREDITO
            $table->date('fecha_comprobante');
            
            // CAMPO NUEVO: Vencimiento para compras a crédito
            $table->date('fecha_vencimiento')->nullable(); 
            
            // CAMPO NUEVO: Observación o glosa para la compra
            $table->text('glosa_o_observacion')->nullable(); 

            // =======================================================
            // 3. Inmutabilidad Histórica del Proveedor (Desnormalización)
            // Esto asegura que la compra mantenga los datos originales del proveedor
            // aunque este cambie su nombre o RUC/NIT en el futuro.
            // =======================================================
            $table->string('proveedor_razon_social', 150)->nullable();
            $table->string('proveedor_nombre_comercial', 150)->nullable();
            $table->string('proveedor_documento_numero', 50)->nullable();

            // =======================================================
            // 4. Totales, Moneda y Control Financiero
            // =======================================================
            
            // CAMPO NUEVO: Moneda y Tipo de Cambio
            $table->string('moneda', 3)->default('PEN'); // ej: PEN, USD, EUR
            $table->decimal('tipo_cambio', 10, 4)->default(1.0000); // Tasa de conversión a moneda base
            
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            
            // CAMPO NUEVO: Control de Pagos
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->string('estado_pago', 30)->default('PENDIENTE'); // PENDIENTE, PAGADO, PARCIAL, ANULADO

            // =======================================================
            // 5. Control y auditoría
            // =======================================================
            $table->boolean('estado')->default(true); // Estado lógico (activo/anulado/cerrado)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Evita duplicidad de comprobantes por proveedor
            $table->unique(['proveedor_id', 'tipo_comprobante', 'numero_comprobante', 'cuenta_id'], 'compra_unica_por_proveedor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
