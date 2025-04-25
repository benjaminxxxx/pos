<?php

namespace Database\Seeders;

use App\Models\Negocio;
use App\Models\Sucursal;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles
        $duenoSistema = Role::create(['name' => 'dueno_sistema']);
        $duenoTienda = Role::create(['name' => 'dueno_tienda']);
        $vendedor = Role::create(['name' => 'vendedor']);

        // Crear permisos
        Permission::create(['name' => 'general']);
        Permission::create(['name' => 'gestionar tiendas']);
        Permission::create(['name' => 'gestionar ventas']);
        Permission::create(['name' => 'gestionar empleados']);

        // Asignar permisos a los roles
        $duenoSistema->givePermissionTo(['general']);
        $duenoTienda->givePermissionTo(['gestionar tiendas', 'gestionar empleados', 'gestionar ventas']);
        $vendedor->givePermissionTo(['gestionar ventas']);

        $admin = User::updateOrCreate(
            ['email' => 'benuserxxx@gmail.com'], // Verifica si ya existe
            [
                'uuid' => Str::uuid(),
                'name' => 'Benjamin Quispe Ramos', 
                'password' => Hash::make(Str::random(20)), // Contraseña aleatoria encriptada
            ]
        );
        $dueno_tienda = User::updateOrCreate(
            ['email' => 'benjaminquispedev@gmail.com'], // Verifica si ya existe
            [
                'uuid' => Str::uuid(),
                'name' => 'Benjamin Quispe Ramos', 
                'password' => Hash::make(Str::random(20)), // Contraseña aleatoria encriptada
                'google_account_id'=>'115072191341407562761'
            ]
        );
        // Asignar rol de dueño del sistema
        $admin->assignRole('dueno_sistema');
        $dueno_tienda->assignRole('dueno_tienda');
        $negocio = Negocio::create([
            'uuid'=> Str::uuid(),
            'user_id'=>$dueno_tienda->id,
            'nombre_legal'=> 'yarech',
            'ruc'=>'23423423434',
            'direccion'=>'AAV SIEMPREVIVA',
            'usuario_sol'=>'AAV SIEMPREVIVA',
            'clave_sol'=>'AAV SIEMPREVIVA',
            'client_secret'=>'secretasdasdasds',
            'modo'=>'produccion',
            'certificado'=>'cert_asdasdsadasd',
            'logo_factura'=>null,
            'tipo_negocio'=>'ferreteria',
        ]);

        $sucursal = Sucursal::create([
            'uuid'=> Str::uuid(),
            'negocio_id'=>$negocio->id,
            'nombre'=> 'tienda principal',
            'direccion'=>'Av los angeles mz g lt 5',
            'telefono'=>null,
            'email'=>null,
            'es_principal'=>true,
            'estado'=>'1',
        ]);

        $this->call([
            TipoComprobanteSeeder::class,
            UnidadSeeder::class,
            CategoriaProductoSeeder::class,
            TiposDocumentosSunatSeeder::class,
            SunatCatalogo51Seeder::class,
            SunatCatalogo7Seeder::class,
        ]);
    }
}
