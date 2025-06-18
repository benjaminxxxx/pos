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

new #[Layout('components.layouts.auth', ['title' => 'Iniciar Sesión'])] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';
    #[Validate('required|string')]
    public string $password = '';
    public bool $remember = false;
    /** * Handle an incoming authentication request. */
    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            LivewireAlert::title('Error!')
                ->text(
                    'Las credenciales no coinciden con ningún
usuario.',
                )
                ->error()
                ->show();
            return;
        }
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
    /** * Ensure the authentication request is not rate limited. */
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
    /** * Get the authentication rate limiting throttle key. */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>


<div class="grid grid-cols-1 md:grid-cols-2 gap-10">
    <div class="hidden md:block">
        <div class="rounded-2xl overflow-hidden max-h-[18rem]">
            <img src="{{ asset('image/portal.jpg') }}" class="object-fit w-full" alt="" />
        </div>
        <div class="p-2 md:p-10">
            <x-auth-header title="Optimiza tu Negocio con un Punto de Venta Ágil y Eficiente"
                description="Gestiona tus ventas con facilidad. Controla inventarios, registra productos y agiliza transacciones en tiempo real, brindando una experiencia rápida, profesional y sin errores." />
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
                <a class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-white text-neutral-800 font-medium text-sm block rounded-lg hover:bg-neutral-100 transition"
                    href="{{ route('google_auth') }}">                    
                    <x-icon-google/> Iniciar con Google
                </a>
            </div>
        </div>

        @if (Route::has('register'))
            <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400 mt-10">
                No tienes una cuenta?
                <flux:link :href="route('register')" wire:navigate>Registrate</flux:link>
            </div>
        @endif
    </div>
</div>
