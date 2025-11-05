<x-layouts.app title="Seleccionar Negocio">
    <x-card class="max-w-md mx-auto mt-10">
        <flux:heading>Selecciona Negocio</flux:heading>
        <form method="POST" action="{{ route('seleccionar-negocio.store') }}">
            @csrf
            <flux:select name="negocio_uuid" required>
                @foreach ($negocios as $negocio)
                    <option value="{{ $negocio->uuid }}">{{ $negocio->nombre_legal }}</option>
                @endforeach
            </flux:select>

            <div class="mt-4 flex justify-end">
                <flux:button type="submit" variant="primary">
                    Continuar
                </flux:button>
            </div>
        </form>
    </x-card>
</x-layouts.app>