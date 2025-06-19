<div>
    <x-loading wire:loading />
    <x-card>
        @if ($negocioSeleccionado)
            <x-flex class="justify-between mb-4">
                <div>
                    <x-h1>Gestión de Correlativos</x-h1>
                    <flux:heading>Negocio: {{ $negocioSeleccionado->nombre_legal }}</flux:heading>
                </div>
                <div class="flex space-x-2">
                    <flux:button wire:click="cambiarNegocio" variant="filled">
                        Cambiar Negocio
                    </flux:button>
                    <flux:button wire:click="create" variant="primary" icon="plus">
                        Nuevo Correlativo
                    </flux:button>
                </div>
            </x-flex>

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

    <!-- Modal para seleccionar negocio -->
    <x-seleccionar-negocio-modal :mostrar="$mostrarModalSeleccionNegocio" :negocios="$negocios" />
</div>
