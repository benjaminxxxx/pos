<div class="space-y-4">
    <x-flex class="justify-between">
        <x-title>Gestión de Correlativos</x-title>
        <flux:button wire:click="create" icon="plus">
            Nuevo Correlativo
        </flux:button>
    </x-flex>
    <x-card>
        @if ($negocio)


            @if ($showForm)
                @include('livewire.dueno_tienda.correlativo_panel.form-correlativo')
            @else
                @include('livewire.dueno_tienda.correlativo_panel.lista-correlativos')
            @endif
        @else
            <x-flex class="flex-col justify-center h-64">
                <x-h2 class="mb-4">No hay negocio seleccionado</x-h2>
                <flux:button wire:click="cambiarNegocio" variant="primary">
                    Seleccionar Negocio
                </flux:button>
            </x-flex>
        @endif
    </x-card>
    <x-loading wire:loading />
</div>
