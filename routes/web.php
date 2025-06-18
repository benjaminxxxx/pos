<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\VentaController;
use App\Livewire\VentaPanel\GestionVentas;
use App\Livewire\VentaPanel\Ventas;
use App\Models\User;
use App\Services\ComprobanteService;
use App\Services\ComprobanteServicio;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Volt\Volt;



Route::view('/', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

});

Route::middleware(['auth', 'role:dueno_sistema'])->group(function () {
    Route::get('/superadmin/clientes', [SuperadminController::class, 'clientes'])->name('superadmin.clientes');
    Route::get('/superadmin/categorias', App\Livewire\Superadmin\Categorias\GestionCategorias::class)->name('superadmin.categorias');
    Route::get('/superadmin/marcas', App\Livewire\Superadmin\Marcas\GestionMarcas::class)->name('superadmin.marcas');
    Route::get('/superadmin/unidades', App\Livewire\Superadmin\Unidades\GestionUnidades::class)->name('superadmin.unidades');
});

Route::middleware(['auth', 'role:dueno_tienda'])->prefix('mi-tienda')->group(function () {
    Route::get('/negocios', App\Livewire\DuenoTienda\NegocioPanel\GestionNegocios::class)->name('dueno_tienda.negocios');
    Route::get('/sucursales', App\Livewire\DuenoTienda\SucursalPanel\GestionSucursales::class)->name('dueno_tienda.sucursales');
    Route::get('/correlativos', App\Livewire\DuenoTienda\CorrelativoPanel\GestionCorrelativos::class)->name('dueno_tienda.correlativos');
    Route::get('/productos', App\Livewire\DuenoTienda\Productos\GestionProductos::class)->name('dueno_tienda.productos');
    Route::get('/servicios', App\Livewire\DuenoTienda\Servicios\GestionServicios::class)->name('dueno_tienda.servicios');
    //modificar esta ruta para que solo accedan los clientes del dueño de la tienda
    Route::get('/clientes', App\Livewire\DuenoTienda\NegocioPanel\GestionNegocios::class)->name('dueno_tienda.clientes');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/vender', GestionVentas::class)
    ->name('vender')
    ->middleware('can:gestionar ventas');
    Route::get('/ventas', Ventas::class)
    ->name('ventas')
    ->middleware('can:gestionar ventas');
});

Route::get('/ver-factura/{serie?}/{numero?}', [FacturaController::class, 'mostrar'])->name('ver_factura');


Route::get('/ventaTest', function () {
    return ComprobanteService::generar(30);
})->name('ventaTest');
Route::get('/notaTest', function () {
    return ComprobanteService::generarNota(1);
})->name('notaTest');

Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/mis-sucursales', [SucursalController::class, 'sucursalesPorUsuario'])->name('listar.sucursales.porusuario');
    Route::get('/mis-productos', [SucursalController::class, 'buscarProductos'])->name('buscar.productos.porsucursal');
    Route::get('/cliente/buscar', [ClienteController::class, 'buscar'])->name('cliente.buscar');
    Route::post('/cliente/crear', [ClienteController::class, 'registrar'])->name('cliente.registrar');
    Route::post('/venta/registrar', [VentaController::class, 'registrar'])->name('venta.registrar');
    Route::get('/venta/listar/{sucursal}', [VentaController::class, 'listar'])->name('venta.listar');
});

Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google_auth');

Route::get('/auth/google/callback', function () {
    $user = Socialite::driver('google')->user();

    $existingUser = User::where('google_account_id', $user->getId())
        ->orWhere('email', $user->getEmail())
        ->first();

    if ($existingUser) {
        // Si el usuario ya existe, actualiza su google_account_id si está vacío
        if (!$existingUser->google_account_id) {
            $existingUser->google_account_id = $user->getId();
            $existingUser->save();
        }

        // Verificar si tiene al menos un rol asignado
        if ($existingUser->roles()->count() === 0) {
            $existingUser->assignRole('dueno_tienda'); // Asignar rol por defecto
        }

        Auth::login($existingUser);
    } else {
        // Crea un nuevo usuario con el rol 'dueno_tienda'
        $newUser = User::create([
            'code' => Str::random(15),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'google_account_id' => $user->getId(),
            'password' => bcrypt(Str::random(16)),
        ]);

        // Asignar el rol por defecto
        $newUser->assignRole('dueno_tienda');

        Auth::login($newUser);
    }

    return to_route('dashboard');  // Redirige al dashboard
});

require __DIR__ . '/auth.php';
