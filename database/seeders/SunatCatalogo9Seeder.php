<?php

namespace Database\Seeders;

use App\Models\SunatCatalogo9;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SunatCatalogo9Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['codigo' => '01', 'descripcion' => 'Anulación de la operación', 'motivo' => 'Se anula completamente la operación por error o cancelación.', 'requiere_detalle' => false],
            ['codigo' => '02', 'descripcion' => 'Anulación por error en el RUC', 'motivo' => 'El RUC del cliente fue ingresado incorrectamente.', 'requiere_detalle' => false],
            ['codigo' => '03', 'descripcion' => 'Corrección por error en la descripción.', 'motivo' => 'Se corrigió un error en la descripción del producto o servicio.', 'requiere_detalle' => true],
            ['codigo' => '04', 'descripcion' => 'Descuento global', 'motivo' => 'Aplicación de un descuento global no considerado inicialmente.', 'requiere_detalle' => false],
            ['codigo' => '05', 'descripcion' => 'Descuento por ítem', 'motivo' => 'Se otorgó un descuento por ítem específico.', 'requiere_detalle' => true],
            ['codigo' => '06', 'descripcion' => 'Devolución total', 'motivo' => 'El cliente devolvió todos los productos adquiridos.', 'requiere_detalle' => false],
            ['codigo' => '07', 'descripcion' => 'Devolución por ítem', 'motivo' => 'Se devolvió solo un ítem de la operación.', 'requiere_detalle' => true],
            ['codigo' => '08', 'descripcion' => 'Bonificación', 'motivo' => 'Se entregó un producto en calidad de bonificación.', 'requiere_detalle' => true],
            ['codigo' => '09', 'descripcion' => 'Disminución en el valor', 'motivo' => 'Se ajustó el valor de la operación por un error o acuerdo posterior.', 'requiere_detalle' => true],
            ['codigo' => '10', 'descripcion' => 'Otros conceptos', 'motivo' => 'Corrección por razones no contempladas en las demás opciones.', 'requiere_detalle' => false],
            ['codigo' => '11', 'descripcion' => 'Ajustes de operaciones de exportación', 'motivo' => 'Ajustes aplicados en operaciones de exportación.', 'requiere_detalle' => false],
            ['codigo' => '12', 'descripcion' => 'Ajustes afectos al IVAP', 'motivo' => 'Corrección en operaciones gravadas con IVAP.', 'requiere_detalle' => false],
            ['codigo' => '13', 'descripcion' => 'Corrección del monto neto pendiente de pago y/o la(s) fecha(s) de vencimiento del pago único o de las cuotas y/o los montos correspondientes a cada cuota, de ser el caso.', 'motivo' => 'Actualización de monto neto pendiente o fechas de pago.', 'requiere_detalle' => false],
        ];

        foreach ($data as $item) {
            SunatCatalogo9::create($item);
        }
    }


}
