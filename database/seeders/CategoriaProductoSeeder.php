<?php

namespace Database\Seeders;

use App\Models\CategoriaProducto;
use Illuminate\Database\Seeder;

class CategoriaProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorías para Ferretería
        $ferreteria = CategoriaProducto::create([
            'descripcion' => 'Ferretería',
            'tipo_negocio' => 'ferreteria',
        ]);

        $subcategoriasFerreteria = [
            'Herramientas Manuales',
            'Herramientas Eléctricas',
            'Materiales de Construcción',
            'Pinturas y Accesorios',
            'Plomería',
            'Electricidad',
            'Cerrajería',
            'Jardinería',
        ];

        foreach ($subcategoriasFerreteria as $subcategoria) {
            CategoriaProducto::create([
                'descripcion' => $subcategoria,
                'categoria_id' => $ferreteria->id,
                'tipo_negocio' => 'ferreteria',
            ]);
        }

        // Categorías para Hotel
        $hotel = CategoriaProducto::create([
            'descripcion' => 'Hotel',
            'tipo_negocio' => 'hotel',
        ]);

        $subcategoriasHotel = [
            'Habitaciones',
            'Servicios de Restaurante',
            'Servicios de Lavandería',
            'Servicios de Spa',
            'Eventos y Conferencias',
            'Transporte',
        ];

        foreach ($subcategoriasHotel as $subcategoria) {
            CategoriaProducto::create([
                'descripcion' => $subcategoria,
                'categoria_id' => $hotel->id,
                'tipo_negocio' => 'hotel',
            ]);
        }

        // Categorías para Panadería
        $panaderia = CategoriaProducto::create([
            'descripcion' => 'Panadería',
            'tipo_negocio' => 'panaderia',
        ]);

        $subcategoriasPanaderia = [
            'Panes',
            'Pasteles',
            'Galletas',
            'Postres',
            'Bebidas',
            'Sándwiches',
        ];

        foreach ($subcategoriasPanaderia as $subcategoria) {
            CategoriaProducto::create([
                'descripcion' => $subcategoria,
                'categoria_id' => $panaderia->id,
                'tipo_negocio' => 'panaderia',
            ]);
        }

        // Categorías para Librería
        $libreria = CategoriaProducto::create([
            'descripcion' => 'Librería',
            'tipo_negocio' => 'libreria',
        ]);

        $subcategoriasLibreria = [
            'Libros',
            'Útiles Escolares',
            'Papelería',
            'Arte y Manualidades',
            'Electrónica',
            'Juegos Educativos',
        ];

        foreach ($subcategoriasLibreria as $subcategoria) {
            CategoriaProducto::create([
                'descripcion' => $subcategoria,
                'categoria_id' => $libreria->id,
                'tipo_negocio' => 'libreria',
            ]);
        }

        // Categorías para Pollería
        $polleria = CategoriaProducto::create([
            'descripcion' => 'Pollería',
            'tipo_negocio' => 'polleria',
        ]);

        $subcategoriasPolleria = [
            'Pollos a la Brasa',
            'Combos Familiares',
            'Guarniciones',
            'Bebidas',
            'Postres',
            'Promociones',
        ];

        foreach ($subcategoriasPolleria as $subcategoria) {
            CategoriaProducto::create([
                'descripcion' => $subcategoria,
                'categoria_id' => $polleria->id,
                'tipo_negocio' => 'polleria',
            ]);
        }
    }
}

