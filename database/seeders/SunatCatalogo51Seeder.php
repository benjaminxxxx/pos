<?php

namespace Database\Seeders;

use App\Models\SunatCatalogo51;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SunatCatalogo51Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['codigo' => '0101', 'descripcion' => 'Venta Interna'],
            ['codigo' => '0102', 'descripcion' => 'Exportación'],
            ['codigo' => '0103', 'descripcion' => 'No Domiciliados'],
            ['codigo' => '0104', 'descripcion' => 'Venta Interna – Anticipos'],
            ['codigo' => '0105', 'descripcion' => 'Venta Itinerante'],
            ['codigo' => '0106', 'descripcion' => 'Factura Guía'],
            ['codigo' => '0107', 'descripcion' => 'Venta Arroz Pilado'],
            ['codigo' => '0108', 'descripcion' => 'Factura - Comprobante de Percepción'],
            ['codigo' => '0110', 'descripcion' => 'Factura - Guía remitente'],
            ['codigo' => '0111', 'descripcion' => 'Factura - Guía transportista'],
        ];

        foreach ($tipos as $tipo) {
            SunatCatalogo51::updateOrCreate(['codigo' => $tipo['codigo']], $tipo);
        }
    }
}
