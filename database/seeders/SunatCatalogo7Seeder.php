<?php

namespace Database\Seeders;

use App\Models\SunatCatalogo7;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SunatCatalogo7Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registros = [
            ['10', 'Gravado - Operación Onerosa', 'gravado', true, 18.00, false, true, true, true, null, true],
            ['11', 'Gravado – Retiro por premio', 'gravado', true, 18.00, true, true, false, false, null, false],
            ['12', 'Gravado – Retiro por donación', 'gravado', true, 18.00, true, true, false, false, null, false],
            ['13', 'Gravado – Retiro', 'gravado', true, 18.00, true, true, false, false, null, true],
            ['14', 'Gravado – Retiro por publicidad', 'gravado', true, 18.00, true, true, false, false, null, false],
            ['15', 'Gravado – Bonificaciones', 'gravado', true, 18.00, true, true, false, false, null, false],
            ['16', 'Gravado – Retiro por entrega a trabajadores', 'gravado', true, 18.00, true, true, false, false, null, false],
            ['17', 'Gravado – IVAP', 'gravado', false, 0.00, false, true, true, false, 'No aplica IGV, solo IVAP', false],
            ['20', 'Exonerado - Operación Onerosa', 'exonerado', false, 0.00, false, true, true, true, null, true],
            ['21', 'Exonerado – Transferencia Gratuita', 'exonerado', false, 0.00, true, true, false, false, null, false],
            ['30', 'Inafecto - Operación Onerosa', 'inafecto', false, 0.00, false, true, true, true, null, true],
            ['31', 'Inafecto – Retiro por Bonificación', 'inafecto', false, 0.00, true, true, false, false, null, false],
            ['32', 'Inafecto – Retiro', 'inafecto', false, 0.00, true, true, false, false, null, true],
            ['33', 'Inafecto – Retiro por Muestras Médicas', 'inafecto', false, 0.00, true, true, false, false, null, false],
            ['34', 'Inafecto - Retiro por Convenio Colectivo', 'inafecto', false, 0.00, true, true, false, false, null, false],
            ['35', 'Inafecto – Retiro por premio', 'inafecto', false, 0.00, true, true, false, false, null, false],
            ['36', 'Inafecto - Retiro por publicidad', 'inafecto', false, 0.00, true, true, false, false, null, false],
            ['40', 'Exportación', 'exportacion', false, 0.00, false, true, true, false, null, false],
        ];

        foreach ($registros as $r) {
            SunatCatalogo7::updateOrCreate(
                ['codigo' => $r[0]],
                [
                    'descripcion' => $r[1],
                    'tipo_afectacion' => $r[2],
                    'aplica_igv' => $r[3],
                    'tasa_igv' => $r[4],
                    'es_gratuito' => $r[5],
                    'considerar_para_operacion' => $r[6],
                    'afecta_base' => $r[7],
                    'uso_comun' => $r[8],
                    'observacion' => $r[9],
                    'estado' => $r[10],
                ]
            );
        }
    }
}
