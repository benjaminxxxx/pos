<?php

namespace Database\Seeders;

use App\Models\TipoDocumentoSunat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposDocumentosSunatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposDocumentos = [
            ['codigo' => '0', 'nombre' => 'Doc. Trib. No Dom. Sin RUC', 'nombre_corto' => 'No Dom. Sin RUC'],
            ['codigo' => '1', 'nombre' => 'Doc. Nacional de Identidad', 'nombre_corto' => 'DNI'],
            ['codigo' => '4', 'nombre' => 'Carnet de Extranjería', 'nombre_corto' => 'Carnet Extranjería'],
            ['codigo' => '6', 'nombre' => 'Registro Único de Contribuyentes', 'nombre_corto' => 'RUC'],
            ['codigo' => '7', 'nombre' => 'Pasaporte', 'nombre_corto' => 'Pasaporte'],
            ['codigo' => 'A', 'nombre' => 'Ced. Diplomática de Identidad', 'nombre_corto' => 'Ced. Diplomática'],
            ['codigo' => 'B', 'nombre' => 'Doc. Identidad País Residencia-No.D', 'nombre_corto' => 'Doc. País Residencia'],
            ['codigo' => 'C', 'nombre' => 'Tax Identification Number - TIN – Doc Trib PP.NN', 'nombre_corto' => 'TIN'],
            ['codigo' => 'D', 'nombre' => 'Identification Number - IN – Doc Trib PP. JJ', 'nombre_corto' => 'IN'],
            ['codigo' => 'E', 'nombre' => 'TAM - Tarjeta Andina de Migración', 'nombre_corto' => 'TAM'],
        ];

        // Insertar los datos utilizando el modelo
        foreach ($tiposDocumentos as $tipo) {
            TipoDocumentoSunat::create($tipo);
        }
    }
}
