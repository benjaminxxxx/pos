<div>
    @if($negocioActual)
    <x-flux::badge color="green" class="mb-4">
        {{ $negocioActual->nombre_legal }}
    </x-flux::badge>
    @endif
</div>