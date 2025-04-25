<?php

namespace Database\Seeders;

use App\Models\TipoComprobante;
use Illuminate\Database\Seeder;

class TipoComprobanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposComprobante = [
            [
                'nombre' => 'Factura',
                'codigo' => '01',
                'descripcion' => 'Factura emitida a clientes',
                'estado' => true,
            ],
            [
                'nombre' => 'Boleta de Venta',
                'codigo' => '03',
                'descripcion' => 'Boleta de venta emitida a clientes',
                'estado' => true,
            ],
            [
                'nombre' => 'Nota de Crédito',
                'codigo' => '07',
                'descripcion' => 'Nota de crédito que modifica una factura',
                'estado' => true,
            ],
            [
                'nombre' => 'Nota de Débito',
                'codigo' => '08',
                'descripcion' => 'Nota de débito que modifica una factura',
                'estado' => true,
            ],
            [
                'nombre' => 'Guía de Remisión - Remitente',
                'codigo' => '09',
                'descripcion' => 'Guía de remisión emitida por el remitente',
                'estado' => true,
            ],
        ];

        foreach ($tiposComprobante as $tipo) {
            TipoComprobante::create($tipo);
        }
    }
}

