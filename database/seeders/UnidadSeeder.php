<?php

namespace Database\Seeders;

use App\Models\Unidad;
use DB;
use Illuminate\Database\Seeder;

class UnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            ['codigo' => '4A', 'descripcion' => 'BOBINAS', 'alt' => 'Bobina'],
            ['codigo' => 'BJ', 'descripcion' => 'BALDE', 'alt' => 'Balde'],
            ['codigo' => 'BLL', 'descripcion' => 'BARRILES', 'alt' => 'Barril'],
            ['codigo' => 'BG', 'descripcion' => 'BOLSA', 'alt' => 'Bolsa'],
            ['codigo' => 'BO', 'descripcion' => 'BOTELLAS', 'alt' => 'Botella'],
            ['codigo' => 'BX', 'descripcion' => 'CAJA', 'alt' => 'Caja'],
            ['codigo' => 'CT', 'descripcion' => 'CARTONES', 'alt' => 'Cartón'],
            ['codigo' => 'CMK', 'descripcion' => 'CENTIMETRO CUADRADO', 'alt' => 'cm²'],
            ['codigo' => 'CMQ', 'descripcion' => 'CENTIMETRO CUBICO', 'alt' => 'cm³'],
            ['codigo' => 'CMT', 'descripcion' => 'CENTIMETRO LINEAL', 'alt' => 'cm'],
            ['codigo' => 'CEN', 'descripcion' => 'CIENTO DE UNIDADES', 'alt' => 'Ciento'],
            ['codigo' => 'CY', 'descripcion' => 'CILINDRO', 'alt' => 'Cilindro'],
            ['codigo' => 'CJ', 'descripcion' => 'CONOS', 'alt' => 'Cono'],
            ['codigo' => 'DZN', 'descripcion' => 'DOCENA', 'alt' => 'Docena'],
            ['codigo' => 'DZP', 'descripcion' => 'DOCENA POR 10**6', 'alt' => 'Docena x millón'],
            ['codigo' => 'BE', 'descripcion' => 'FARDO', 'alt' => 'Fardo'],
            ['codigo' => 'GLI', 'descripcion' => 'GALON INGLES (4,545956L)', 'alt' => 'Galón Ing.'],
            ['codigo' => 'GRM', 'descripcion' => 'GRAMO', 'alt' => 'g'],
            ['codigo' => 'GRO', 'descripcion' => 'GRUESA', 'alt' => 'Gruesa'],
            ['codigo' => 'HLT', 'descripcion' => 'HECTOLITRO', 'alt' => 'hL'],
            ['codigo' => 'LEF', 'descripcion' => 'HOJA', 'alt' => 'Hoja'],
            ['codigo' => 'SET', 'descripcion' => 'JUEGO', 'alt' => 'Juego'],
            ['codigo' => 'KGM', 'descripcion' => 'KILOGRAMO', 'alt' => 'kg'],
            ['codigo' => 'KTM', 'descripcion' => 'KILOMETRO', 'alt' => 'km'],
            ['codigo' => 'KWH', 'descripcion' => 'KILOVATIO HORA', 'alt' => 'kWh'],
            ['codigo' => 'KT', 'descripcion' => 'KIT', 'alt' => 'Kit'],
            ['codigo' => 'CA', 'descripcion' => 'LATAS', 'alt' => 'Lata'],
            ['codigo' => 'LBR', 'descripcion' => 'LIBRAS', 'alt' => 'lb'],
            ['codigo' => 'LTR', 'descripcion' => 'LITRO', 'alt' => 'L'],
            ['codigo' => 'MWH', 'descripcion' => 'MEGAWATT HORA', 'alt' => 'MWh'],
            ['codigo' => 'MTR', 'descripcion' => 'METRO', 'alt' => 'm'],
            ['codigo' => 'MTK', 'descripcion' => 'METRO CUADRADO', 'alt' => 'm²'],
            ['codigo' => 'MTQ', 'descripcion' => 'METRO CUBICO', 'alt' => 'm³'],
            ['codigo' => 'MGM', 'descripcion' => 'MILIGRAMOS', 'alt' => 'mg'],
            ['codigo' => 'MLT', 'descripcion' => 'MILILITRO', 'alt' => 'ml'],
            ['codigo' => 'MMT', 'descripcion' => 'MILIMETRO', 'alt' => 'mm'],
            ['codigo' => 'MMK', 'descripcion' => 'MILIMETRO CUADRADO', 'alt' => 'mm²'],
            ['codigo' => 'MMQ', 'descripcion' => 'MILIMETRO CUBICO', 'alt' => 'mm³'],
            ['codigo' => 'MLL', 'descripcion' => 'MILLARES', 'alt' => 'Millar'],
            ['codigo' => 'UM', 'descripcion' => 'MILLON DE UNIDADES', 'alt' => 'Millón'],
            ['codigo' => 'ONZ', 'descripcion' => 'ONZAS', 'alt' => 'oz'],
            ['codigo' => 'PF', 'descripcion' => 'PALETAS', 'alt' => 'Paleta'],
            ['codigo' => 'PK', 'descripcion' => 'PAQUETE', 'alt' => 'Paquete'],
            ['codigo' => 'PR', 'descripcion' => 'PAR', 'alt' => 'Par'],
            ['codigo' => 'FOT', 'descripcion' => 'PIES', 'alt' => 'ft'],
            ['codigo' => 'FTK', 'descripcion' => 'PIES CUADRADOS', 'alt' => 'ft²'],
            ['codigo' => 'FTQ', 'descripcion' => 'PIES CUBICOS', 'alt' => 'ft³'],
            ['codigo' => 'C62', 'descripcion' => 'PIEZAS', 'alt' => 'Pieza'],
            ['codigo' => 'PG', 'descripcion' => 'PLACAS', 'alt' => 'Placa'],
            ['codigo' => 'ST', 'descripcion' => 'PLIEGO', 'alt' => 'Pliego'],
            ['codigo' => 'INH', 'descripcion' => 'PULGADAS', 'alt' => 'in'],
            ['codigo' => 'RM', 'descripcion' => 'RESMA', 'alt' => 'Resma'],
            ['codigo' => 'DR', 'descripcion' => 'TAMBOR', 'alt' => 'Tambor'],
            ['codigo' => 'STN', 'descripcion' => 'TONELADA CORTA', 'alt' => 'ton (corta)'],
            ['codigo' => 'LTN', 'descripcion' => 'TONELADA LARGA', 'alt' => 'ton (larga)'],
            ['codigo' => 'TNE', 'descripcion' => 'TONELADAS', 'alt' => 'ton'],
            ['codigo' => 'TU', 'descripcion' => 'TUBOS', 'alt' => 'Tubo'],
            ['codigo' => 'NIU', 'descripcion' => 'UNIDAD (BIENES)', 'alt' => 'Und.'],
            ['codigo' => 'ZZ', 'descripcion' => 'UNIDAD (SERVICIOS)', 'alt' => 'Serv.'],
            ['codigo' => 'GLL', 'descripcion' => 'US GALON (3,7843 L)', 'alt' => 'Galón'],
            ['codigo' => 'YRD', 'descripcion' => 'YARDA', 'alt' => 'yd'],
            ['codigo' => 'YDK', 'descripcion' => 'YARDA CUADRADA', 'alt' => 'yd²'],
        ];

        DB::table('unidades')->insert($unidades);
    }
}

