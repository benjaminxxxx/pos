<?php

namespace App\Providers;

use App\Models\Compra;
use App\Models\Cuenta;
use App\Models\MovimientoCaja;
use App\Models\TipoMovimiento;
use App\Models\Venta;
use App\Services\Caja\MovimientoCajaServicio;
use DB;
use Exception;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifica tu correo electrónico')
                ->greeting('Hola ' . $notifiable->name . '!')
                ->line('Haz clic en el botón para confirmar tu dirección de correo.')
                ->action('Verificar correo', $url)
                ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.');
        });

        // Evento al iniciar sesión
        Event::listen(Authenticated::class, function ($event) {
            $user = $event->user;

            if ($user->hasRole('dueno_tienda')) {

                // Buscar o crear la cuenta del dueño
                $cuenta = $user->cuenta()->first();

                if (!$cuenta) {
                    $ultimaCuenta = Cuenta::latest('id')->first();
                    $indice = $ultimaCuenta ? $ultimaCuenta->id + 1 : 1;
                    $nombreCuenta = 'CUENTA' . str_pad($indice, 4, '0', STR_PAD_LEFT);

                    $cuenta = Cuenta::create([
                        'dueno_id' => $user->id,
                        'nombre' => $nombreCuenta,
                        'plan' => 'GRATUITO',
                        'estado_pago' => 'AL_DIA',
                        'metodo_pago' => 'OTRO',
                        'fecha_inicio_plan' => now(),
                        'fecha_vencimiento_plan' => null,
                        'costo_plan' => null,
                        'configuracion_pago_json' => null,
                        'estado' => 'ACTIVO',
                    ]);
                }

                // Vincular la cuenta a todos los negocios del usuario que no tengan cuenta
                $user->negocios()
                    ->whereNull('cuenta_id')
                    ->update(['cuenta_id' => $cuenta->id]);
            }

            //registro de ingreso temporal
            $ventas = Venta::where('flag_contabilizado', false)
                ->whereHas('negocio', function ($q) {
                    $q->whereNotNull('cuenta_id');
                })
                ->with(['cuenta'])->get();

            $tipoVenta = TipoMovimiento::where('slug', 'venta_sistema')->firstOrFail();
            $tipoAnulacion = TipoMovimiento::where('slug', 'anulacion_venta')->firstOrFail();

            foreach ($ventas as $venta) {
                try {
                    DB::transaction(function () use ($venta, $tipoVenta, $tipoAnulacion) {
                        $duenoCuenta = $venta->negocio->cuenta?->dueno_id;
                        if (!$duenoCuenta) {
                            $duenoCuenta = (int) $venta->negocio->user_id;
                        }
                        if (!$duenoCuenta) {

                            throw new Exception('No hay un dueño de cuenta especifico');
                        }

                        app(MovimientoCajaServicio::class)->registrar([
                            'tipo_movimiento_id' => $tipoVenta->id,
                            'cuenta_id' => $venta->cuenta->id,
                            'sucursal_id' => $venta->sucursal_id,
                            'usuario_id' => $duenoCuenta,
                            'monto' => $venta->monto_importe_venta,
                            'metodo_pago' => $venta->metodo_pago,
                            'referencia_tipo' => Venta::class,
                            'referencia_id' => $venta->id,
                            'observacion' => "Venta histórica folio #{$venta->id}",
                        ]);

                        if ($venta->estado === 'anulado') {
                            app(MovimientoCajaServicio::class)->registrar([
                                'tipo_movimiento_id' => $tipoAnulacion->id,
                                'cuenta_id' => $venta->cuenta->id,
                                'sucursal_id' => $venta->sucursal_id,
                                'usuario_id' => $duenoCuenta,
                                'monto' => $venta->monto_importe_venta,
                                'metodo_pago' => $venta->metodo_pago,
                                'observacion' => "Venta histórica anulada #{$venta->id}",
                                'referencia_tipo' => Venta::class,
                                'referencia_id' => $venta->id,
                                'fecha' => now(),
                            ]);
                        }

                        $venta->update(['flag_contabilizado' => true]);
                    });

                } catch (\Throwable $e) {
                    report($e);
                    continue;
                }
            }

            $compras = Compra::where('flag_contabilizado', false)
                ->with(['cuenta'])
                ->get();

            // 🔹 Tipos de movimiento del sistema
            $tipoCompra = TipoMovimiento::where('slug', 'compra_sistema')->firstOrFail();

            foreach ($compras as $compra) {
                try {
                    DB::transaction(function () use ($compra, $tipoCompra, $tipoAnulacion) {

                        // 🔹 Determinar usuario responsable (dueño de la cuenta)
                        $duenoCuenta = $compra->cuenta?->dueno_id;

                        if (!$duenoCuenta) {
                            throw new Exception('No hay un dueño de cuenta válido para la compra');
                        }

                        // 🔹 Evitar duplicados (blindaje)
                        $yaExiste = MovimientoCaja::where('referencia_tipo', Compra::class)
                            ->where('referencia_id', $compra->id)
                            ->exists();

                        if ($yaExiste) {
                            $compra->update(['flag_contabilizado' => true]);
                            return;
                        }

                        // 🔹 Registrar EGRESO si fue contado
                        if ($compra->total > 0 && $compra->forma_pago === 'CONTADO') {
                            app(MovimientoCajaServicio::class)->registrar([
                                'tipo_movimiento_id' => $tipoCompra->id,
                                'cuenta_id' => $compra->cuenta->id,
                                'sucursal_id' => $compra->sucursal_id,
                                'usuario_id' => $duenoCuenta,
                                'monto' => $compra->total,
                                'metodo_pago' => $compra->forma_pago,
                                'observacion' => "Compra histórica N° {$compra->id}",
                                'referencia_tipo' => Compra::class,
                                'referencia_id' => $compra->id,
                                'fecha' => now(),
                            ]);
                        }

                        // 🔹 Marcar como contabilizada SOLO si todo salió bien
                        $compra->update(['flag_contabilizado' => true]);
                    });

                } catch (\Throwable $e) {
                    report($e);
                    continue;
                }
            }

        });
    }
}
