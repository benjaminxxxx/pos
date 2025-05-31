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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
        
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('nombre_cliente')->nullable();
            $table->string('documento_cliente')->nullable();
            $table->string('tipo_documento_cliente')->nullable();
        
            $table->enum('estado', ['carrito', 'pagado', 'anulado', 'deuda'])->default('carrito');
            $table->enum('modo_venta', ['desarrollo', 'produccion'])->default('produccion');
        
            $table->string('tipo_factura', 4)->nullable();

            $table->decimal('monto_operaciones_gravadas', 12, 2)->default(0);
            $table->decimal('monto_operaciones_exoneradas', 12, 2)->default(0);
            $table->decimal('monto_operaciones_inafectas', 12, 2)->default(0);
            $table->decimal('monto_operaciones_exportacion', 12, 2)->default(0);
            $table->decimal('monto_operaciones_gratuitas', 12, 2)->default(0);
            $table->decimal('monto_igv', 10, 2)->nullable();
            $table->decimal('monto_igv_gratuito', 10, 2)->nullable();
            $table->decimal('icbper', 10, 2)->nullable()->default(0);  
            $table->decimal('total_impuestos', 10, 2)->nullable()->default(0);  
            

            $table->decimal('valor_venta', 10, 2)->nullable();
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->decimal('redondeo', 10, 2)->nullable();
            $table->decimal('monto_importe_venta', 10, 2)->nullable();          
        
            $table->string('tipo_comprobante_codigo')->nullable();
            $table->string('serie_comprobante')->nullable();
            $table->string('correlativo_comprobante')->nullable();
        
            $table->text('sunat_comprobante_pdf')->nullable();
            $table->text('voucher_pdf')->nullable();
            $table->text('sunat_xml_firmado')->nullable();
            $table->text('sunat_cdr')->nullable();
        
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
        
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_pago')->nullable();

            $table->foreignId('negocio_id')->constrained('negocios')->onDelete('cascade');
            $table->foreign('tipo_comprobante_codigo')->references('codigo')->on('tipo_comprobantes')->onDelete('set null');
            $table->foreign('tipo_factura')->references('codigo')->on('sunat_catalogo_51')->onDelete('set null');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
