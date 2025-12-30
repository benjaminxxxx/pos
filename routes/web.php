<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\SeleccionarNegocioController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\VentaController;
use App\Livewire\Actualizaciones;
use App\Livewire\DuenoTienda\ClientePanel\GestionClientes;
use App\Livewire\DuenoTienda\ProveedorPanel\GestionProveedores;
use App\Livewire\VentaPanel\GestionVentas;
use App\Livewire\VentaPanel\Ventas;
use App\Models\Presentacion;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaMetodoPago;
use App\Services\ComprobanteService;
use App\Services\ComprobanteServicio;
use App\Services\ComprobanteSinSunatServicio;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Volt\Volt;
use Illuminate\Http\Request;


Route::view('/', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/clave/{texto?}', function ($texto = '12345678') {
    dd([
        'texto' => $texto,
        'hash' => Hash::make($texto),
    ]);
});
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
    Route::get('/negocios', function () {
        return view('livewire.dueno_tienda.negocio_panel.index-negocios');
    })->name('dueno_tienda.negocios');

    Route::get('/registrar_movimiento', function () {
        return view('livewire.dueno_tienda.movimiento_panel.index-movimiento-form');
    })->name('dueno_tienda.registrar_movimiento');

    Route::get('/movimientos', function () {
        return view('livewire.dueno_tienda.movimiento_panel.index-movimientos');
    })->name('dueno_tienda.movimientos');


    Route::get('/sucursales', App\Livewire\DuenoTienda\SucursalPanel\GestionSucursales::class)->name('dueno_tienda.sucursales');
    Route::get('/correlativos', App\Livewire\DuenoTienda\CorrelativoPanel\GestionCorrelativos::class)->name('dueno_tienda.correlativos');
    Route::get('/productos', function () {
        return view('livewire.dueno_tienda.productos.index-productos');
    })->name('dueno_tienda.productos');
    Route::get('/precios_preferenciales', function () {
        return view('livewire.dueno_tienda.precios_preferenciales.index-precios_preferenciales');
    })->name('dueno_tienda.precios_preferenciales');
    Route::get('/configuracion/disenio_impresion', function () {
        return view('livewire.dueno_tienda.configuracion.index-disenio_impresion');
    })->name('dueno_tienda.configuracion.disenio_impresion');


    Route::get('/servicios', App\Livewire\DuenoTienda\Servicios\GestionServicios::class)->name('dueno_tienda.servicios');
    //modificar esta ruta para que solo accedan los clientes del dueño de la tienda
    Route::get('/clientes', GestionClientes::class)->name('dueno_tienda.clientes');
    Route::get('/proveedores', function () {
        return view('livewire.dueno_tienda.proveedores_panel.index-proveedores');
    })->name('dueno_tienda.proveedores');
    Route::get('/compras/entrada_productos', function () {
        return view('livewire.dueno_tienda.almacen.entrada_productos.index-entrada-productos');
    })->name('dueno_tienda.entrada_productos');
    Route::get('/compras/salida_productos', function () {
        return view('livewire.dueno_tienda.almacen.salida_productos.index-salida-productos');
    })->name('dueno_tienda.salida_productos');
    //compras
    Route::get('/compras/realizar_compras', function () {
        return view('livewire.dueno_tienda.compras.index-realizar_compras');
    })->name('dueno_tienda.realizar_compras');
});

Route::get('/seleccionar-negocio', [SeleccionarNegocioController::class, 'index'])
    ->name('seleccionar-negocio');
Route::post('/seleccionar-negocio', [SeleccionarNegocioController::class, 'store'])
    ->name('seleccionar-negocio.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/vender', function () {
        return view('livewire.venta_panel.index-gestion-ventas');
    })->name('vender')
        ->middleware('can:gestionar ventas');

    Route::get('/ventas', function () {
        return view('livewire.venta_panel.index-gestion-historia-ventas');
    })
        ->name('ventas')
        ->middleware('can:gestionar ventas');

    Route::post('/ventas/duplicar', function (Request $request) {
        $uuid = $request->uuid;
        $venta = Venta::with(['detalles.producto'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $data = [
            'detalles' => $venta->detalles->map(function ($detalle) {
                $presentacion = Presentacion::where('producto_id', $detalle->producto_id)
                    ->where('unidad', $detalle->unidad)
                    ->where('factor', $detalle->factor)
                    ->first();

                return [
                    'producto' => [
                        'id' => $detalle->producto_id,
                        'descripcion' => $detalle->producto->descripcion,
                        'unidad' => $detalle->unidad,
                        'monto_venta' => $detalle->monto_precio_unitario,
                        'porcentaje_igv' => $detalle->porcentaje_igv,
                        'tipo_afectacion_igv' => $detalle->tipo_afectacion_igv,
                        'cantidad' => (float) $detalle->cantidad,
                    ],
                    'presentacion' => $presentacion ? [
                        'id' => $presentacion->id,
                        'unidad' => $presentacion->unidad,
                        'factor' => $presentacion->factor,
                        'descripcion' => $presentacion->descripcion,
                        'precio' => $presentacion->precio,
                    ] : null,
                ];
            })->toArray(),
        ];
        $ventaJson = json_encode($data);
        //dd($ventaJson);
        // Guardamos temporalmente en sesión (solo para el próximo request)
        session()->flash('ventaDuplicada', $ventaJson);

        // Redirigimos al panel limpio, sin UUID en la URL
        return redirect()->route('vender');
    })->name('venta_panel.ventas.duplicar')
        ->middleware(['auth', 'can:gestionar ventas']);

    Route::get('/ventas/duplicar/{uuid}', function ($uuid, Request $request) {
        $venta = Venta::with(['detalles.producto'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $data = [
            'detalles' => $venta->detalles->map(function ($detalle) {
                $presentacion = Presentacion::where('producto_id', $detalle->producto_id)
                    ->where('unidad', $detalle->unidad)
                    ->where('factor', $detalle->factor)
                    ->first();

                return [
                    'producto' => [
                        'id' => $detalle->producto_id,
                        'descripcion' => $detalle->producto->descripcion,
                        'unidad' => $detalle->unidad,
                        'monto_venta' => $detalle->monto_precio_unitario,
                        'porcentaje_igv' => $detalle->porcentaje_igv,
                        'tipo_afectacion_igv' => $detalle->tipo_afectacion_igv,
                        'cantidad' => (float) $detalle->cantidad,
                    ],
                    'presentacion' => $presentacion ? [
                        'id' => $presentacion->id,
                        'unidad' => $presentacion->unidad,
                        'factor' => $presentacion->factor,
                        'descripcion' => $presentacion->descripcion,
                        'precio' => $presentacion->precio,
                    ] : null,
                ];
            })->toArray(),
        ];

        // Guardamos temporalmente en sesión
        session()->flash('ventaDuplicada', json_encode($data));

        // Redirigimos al panel sin el UUID
        return redirect()->route('vender');
    })->name('venta_panel.ventas.duplicar.get')
        ->middleware(['auth', 'can:gestionar ventas']);


});

Route::get('/ver-factura/{serie?}/{numero?}', [FacturaController::class, 'mostrar'])->name('ver_factura');


Route::get('/ventaTest', function () {
    return ComprobanteService::generar(30);
})->name('ventaTest');
Route::get('/notaTest', function () {
    return ComprobanteService::generarNota(1);
})->name('notaTest');
Route::get('/ticketTest', function () {
    return ComprobanteSinSunatServicio::generarTicket(115);
})->name('ticketTest');

Route::get('/actualizaciones', Actualizaciones::class)->name('actualizaciones');

Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/mis-sucursales', [SucursalController::class, 'sucursalesPorUsuario'])->name('listar.sucursales.porusuario');
    Route::get('/mis-negocios', [SucursalController::class, 'negociosPorUsuario'])->name('listar.negocios.porusuario');
    Route::get('/mis-productos', [SucursalController::class, 'buscarProductos'])->name('buscar.productos.porsucursal');
    Route::get('/cliente/buscar', [ClienteController::class, 'buscar'])->name('cliente.buscar');
    Route::post('/cliente/sunat', [ClienteController::class, 'sunatPorRuc']);
    Route::post('/cliente/crear', [ClienteController::class, 'registrar'])->name('cliente.registrar');
    Route::post('/venta/registrar', [VentaController::class, 'registrar'])->name('venta.registrar');
    Route::get('/venta/listar/{negocio}/{sucursal?}', [VentaController::class, 'listar'])->name('venta.listar');
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
