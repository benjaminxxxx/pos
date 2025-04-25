<?php

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]

    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            LivewireAlert::title('Error!')
            ->text('Las credenciales no coinciden con ningún usuario.')
            ->error()
            ->show();

            return;
        }

        

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>


<div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <div class="hidden md:block">
        <div class="rounded-2xl overflow-hidden max-h-[20rem]">
            <img src="{{ asset('imagen/panel1.jpg') }}" class="object-fit" alt="">
        </div>
        <div class="p-2 md:p-10">
            <x-auth-header title="Optimiza tu Ferretería con un Punto de Venta Ágil y Eficiente"
                description="Gestiona cada venta con facilidad. Registra productos, controla inventarios y agiliza transacciones en tiempo real, asegurando una experiencia rápida y sin errores." />

        </div>
    </div>
    <div class="bg-panel-primary md:rounded-2xl p-5 md:px-20 md:py-15 dark:bg-neutral-700">

        <x-auth-header title="Iniciar Sesión" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="login" class="flex flex-col gap-6">
            <!-- Email Address -->
            <flux:input wire:model="email" type="email" class="mt-10" required autofocus autocomplete="email"
                placeholder="Email" />

            <!-- Password -->
            <div class="relative">

                <flux:input wire:model="password" type="password" class="mt-2" required
                    autocomplete="current-password" placeholder="Contraseña" />

                @if (Route::has('password.request'))
                    <div class="flex justify-end mt-4">
                        <flux:link class="text-sm text-right w-full" :href="route('password.request')" wire:navigate>
                            Olvidaste tu contraseña?
                        </flux:link>
                    </div>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox wire:model="remember" :label="__('Remember me')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">Iniciar</flux:button>
            </div>
        </form>

        <flux:separator text="ó" class="my-7" />

        <div>
            <div class="flex items-center justify-end">
                <a
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-white text-neutral-800 font-medium text-sm block rounded-lg hover:bg-neutral-100 transition"
                    href="{{route('google_auth')}}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4" />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853" />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05" />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335" />
                        <path d="M1 1h22v22H1z" fill="none" />
                    </svg> Iniciar con Google
                </a>

            </div>
        </div>

        @if (Route::has('register'))
            <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400 my-10">
                No tienes una cuenta?
                <flux:link :href="route('register')" wire:navigate>Registrate</flux:link>
            </div>
        @endif
    </div>


</div>
