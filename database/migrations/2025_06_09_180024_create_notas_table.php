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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_doc'); // 07: crédito, 08: débito
            $table->string('serie_comprobante')->nullable();
            $table->string('correlativo_comprobante')->nullable();
            $table->date('fecha_emision')->nullable();

            $table->string('tip_doc_afectado'); // '01' = Factura, '03' = Boleta
            $table->string('num_doc_afectado'); // Ej: F001-111

            $table->string('cod_motivo'); // Código de motivo (Catálogo 09)
            $table->string('des_motivo'); // Descripción del motivo

            $table->string('tipo_moneda')->default('PEN');

            // Datos monetarios
            $table->decimal('mto_oper_gravadas', 12, 2)->default(0)->nullable();
            $table->decimal('mto_igv', 12, 2)->default(0)->nullable();
            $table->decimal('total_impuestos', 12, 2)->default(0)->nullable();
            $table->decimal('mto_imp_venta', 12, 2)->default(0)->nullable();

            // Campos relacionados (cliente/empresa)
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();

            // Forma de pago (opcional para nota tipo 13)
            $table->string('forma_pago')->nullable(); // 'Contado', 'Credito'
            $table->json('cuotas')->nullable(); // Array JSON de cuotas (monto y fecha)

            // Guias (opcional)
            $table->json('guias')->nullable(); // Array JSON con tipo_doc y nro_doc

            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('negocio_id')->constrained('negocios')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();

            $table->text('sunat_comprobante_pdf')->nullable();
            $table->text('voucher_pdf')->nullable();
            $table->text('sunat_xml_firmado')->nullable();
            $table->text('sunat_cdr')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
