<?php

namespace App\Providers;

use App\Models\Cuenta;
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

            // Solo para dueños de tienda
            if ($user->hasRole('dueno_tienda')) {

                // Revisar si tiene alguna cuenta vinculada
                if (!$user->cuenta()->exists()) {

                    // Generar nombre de cuenta secuencial
                    $ultimaCuenta = Cuenta::latest('id')->first();
                    $indice = $ultimaCuenta ? $ultimaCuenta->id + 1 : 1;
                    $nombreCuenta = 'CUENTA' . str_pad($indice, 4, '0', STR_PAD_LEFT);

                    // Crear cuenta por defecto
                    Cuenta::create([
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
            }
        });
    }
}
