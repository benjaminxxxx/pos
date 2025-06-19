<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth', ['title' => 'Registro'])] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // 🔒 Asignar rol de 'dueno_tienda'
        $user->assignRole('dueno_tienda');

        // Enviar evento de registro (para verificación de correo)
        event(new Registered($user));

        /*
        COdigo Nativo
        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
        */
        Auth::login($user);

        if (!$user->hasVerifiedEmail()) {
            $this->redirectIntended(route('verification.notice'), navigate: true);
        } else {
            $this->redirectIntended(route('dashboard'), navigate: true);
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header title="Crea una cuenta para tu negocio"
        description="Ingresa tus datos a continuación para crear tu cuenta" />
    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Nombre -->
        <flux:input wire:model="name" label="Nombre" type="text" required autofocus autocomplete="name"
            placeholder="Nombre completo" />

        <!-- Correo electrónico -->
        <flux:input wire:model="email" label="Correo electrónico" type="email" required autocomplete="email"
            placeholder="correo@ejemplo.com" />

        <!-- Contraseña -->
        <flux:input wire:model="password" label="Contraseña" type="password" required autocomplete="new-password"
            placeholder="Contraseña" />

        <!-- Confirmar contraseña -->
        <flux:input wire:model="password_confirmation" label="Confirmar contraseña" type="password" required
            autocomplete="new-password" placeholder="Confirmar contraseña" />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                Crear cuenta
            </flux:button>
        </div>

    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>Entrar</flux:link>
    </div>
</div>
