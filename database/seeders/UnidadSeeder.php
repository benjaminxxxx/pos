<?php

namespace Database\Seeders;

use App\Models\Unidad;
use Illuminate\Database\Seeder;

class UnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            // Unidades generales
            ['nombre' => 'Unidad', 'abreviatura' => 'UND', 'tipo_negocio' => null],
            ['nombre' => 'Kilogramo', 'abreviatura' => 'KG', 'tipo_negocio' => null],
            ['nombre' => 'Gramo', 'abreviatura' => 'G', 'tipo_negocio' => null],
            ['nombre' => 'Litro', 'abreviatura' => 'L', 'tipo_negocio' => null],
            ['nombre' => 'Mililitro', 'abreviatura' => 'ML', 'tipo_negocio' => null],
            ['nombre' => 'Metro', 'abreviatura' => 'M', 'tipo_negocio' => null],
            ['nombre' => 'Centímetro', 'abreviatura' => 'CM', 'tipo_negocio' => null],
            ['nombre' => 'Docena', 'abreviatura' => 'DOC', 'tipo_negocio' => null],
            ['nombre' => 'Caja', 'abreviatura' => 'CJA', 'tipo_negocio' => null],
            ['nombre' => 'Paquete', 'abreviatura' => 'PQT', 'tipo_negocio' => null],
            ['nombre' => 'Bolsa', 'abreviatura' => 'BLS', 'tipo_negocio' => null],
            
            // Ferretería
            ['nombre' => 'Galón', 'abreviatura' => 'GAL', 'tipo_negocio' => 'ferreteria'],
            ['nombre' => 'Balde', 'abreviatura' => 'BLD', 'tipo_negocio' => 'ferreteria'],
            ['nombre' => 'Rollo', 'abreviatura' => 'ROL', 'tipo_negocio' => 'ferreteria'],
            ['nombre' => 'Pieza', 'abreviatura' => 'PZA', 'tipo_negocio' => 'ferreteria'],
            
            // Panadería
            ['nombre' => 'Bandeja', 'abreviatura' => 'BDJ', 'tipo_negocio' => 'panaderia'],
            ['nombre' => 'Porción', 'abreviatura' => 'POR', 'tipo_negocio' => 'panaderia'],
            
            // Hotel
            ['nombre' => 'Habitación', 'abreviatura' => 'HAB', 'tipo_negocio' => 'hotel'],
            ['nombre' => 'Noche', 'abreviatura' => 'NCH', 'tipo_negocio' => 'hotel'],
            
            // Librería
            ['nombre' => 'Resma', 'abreviatura' => 'RSM', 'tipo_negocio' => 'libreria'],
            ['nombre' => 'Cuaderno', 'abreviatura' => 'CUA', 'tipo_negocio' => 'libreria'],
            
            // Pollería
            ['nombre' => 'Pollo', 'abreviatura' => 'POL', 'tipo_negocio' => 'polleria'],
            ['nombre' => 'Combo', 'abreviatura' => 'CMB', 'tipo_negocio' => 'polleria'],
        ];

        foreach ($unidades as $unidad) {
            Unidad::create($unidad);
        }
    }
}

