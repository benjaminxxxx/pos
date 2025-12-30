<?php

namespace Database\Seeders;

use App\Models\TipoMovimiento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoMovimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [

            // =====================
            // VENTAS Y CLIENTES
            // =====================
            [
                'slug' => 'venta_sistema',
                'codigo' => 'VENTA',
                'nombre' => 'Venta al contado',
                'tipo_flujo' => 'ingreso',
            ],
            [
                'slug' => 'cobro_credito_cliente',
                'codigo' => 'COBRO CRÉD',
                'nombre' => 'Cobro de venta a crédito',
                'tipo_flujo' => 'ingreso',
            ],
            [
                'slug' => 'anulacion_venta',
                'codigo' => 'ANULACIÓN',
                'nombre' => 'Anulación / devolución de venta',
                'tipo_flujo' => 'egreso',
            ],

            // =====================
            // COMPRAS Y PROVEEDORES
            // =====================
            [
                'slug' => 'compra_sistema',
                'codigo' => 'COMPRA',
                'nombre' => 'Compra al contado',
                'tipo_flujo' => 'egreso',
            ],
            [
                'slug' => 'pago_proveedor',
                'codigo' => 'PAGO PROV',
                'nombre' => 'Pago a proveedor',
                'tipo_flujo' => 'egreso',
            ],

            // =====================
            // CAJA Y AJUSTES
            // =====================
            [
                'slug' => 'ingreso_manual',
                'codigo' => 'INGRESO',
                'nombre' => 'Ingreso manual de caja',
                'tipo_flujo' => 'ingreso',
            ],
            [
                'slug' => 'retiro_caja',
                'codigo' => 'RETIRO',
                'nombre' => 'Retiro de caja',
                'tipo_flujo' => 'egreso',
            ],
            [
                'slug' => 'ajuste_caja_positivo',
                'codigo' => 'AJUSTE +',
                'nombre' => 'Ajuste positivo de caja',
                'tipo_flujo' => 'ingreso',
            ],
            [
                'slug' => 'ajuste_caja_negativo',
                'codigo' => 'AJUSTE -',
                'nombre' => 'Ajuste negativo de caja',
                'tipo_flujo' => 'egreso',
            ],

            // =====================
            // ERRORES Y PÉRDIDAS
            // =====================
            [
                'slug' => 'error_vuelto',
                'codigo' => 'VUELTO -',
                'nombre' => 'Error de vuelto',
                'tipo_flujo' => 'egreso',
            ],
            [
                'slug' => 'robo',
                'codigo' => 'ROBO',
                'nombre' => 'Pérdida por robo',
                'tipo_flujo' => 'egreso',
            ],

            // =====================
            // GASTOS OPERATIVOS
            // =====================
            [
                'slug' => 'gasto_operativo',
                'codigo' => 'GASTO',
                'nombre' => 'Gasto operativo',
                'tipo_flujo' => 'egreso',
            ],
            [
                'slug' => 'pago_personal',
                'codigo' => 'SUELDO',
                'nombre' => 'Pago de personal',
                'tipo_flujo' => 'egreso',
            ],

            // =====================
            // CAPITAL Y FINANZAS
            // =====================
            [
                'slug' => 'inyeccion_capital',
                'codigo' => 'CAPITAL +',
                'nombre' => 'Inyección de capital',
                'tipo_flujo' => 'ingreso',
            ],
            [
                'slug' => 'pago_prestamo',
                'codigo' => 'PRÉSTAMO',
                'nombre' => 'Pago de préstamo',
                'tipo_flujo' => 'egreso',
            ],

            // =====================
            // DESCUENTOS Y RECUPEROS
            // =====================
            [
                'slug' => 'descuento_sueldo',
                'codigo' => 'DESC SUELDO',
                'nombre' => 'Descuento de sueldo por inasistencia',
                'tipo_flujo' => 'ingreso',
            ],

        ];

        foreach ($tipos as $tipo) {
            TipoMovimiento::firstOrCreate(
                ['slug' => $tipo['slug']],
                array_merge($tipo, [
                    'es_sistema' => true,
                    'activo' => true,
                    'cuenta_id' => null,
                ])
            );
        }
    }
}
