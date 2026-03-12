<div>
    @if ($negocioActual)
        <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')">
            {{ $negocioActual->nombre_legal }}
        </flux:navbar.item>
    @endif
</div>
