<?php

namespace App\Providers;

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
    }
}
