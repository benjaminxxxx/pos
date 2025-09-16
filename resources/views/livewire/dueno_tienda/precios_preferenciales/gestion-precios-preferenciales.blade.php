<div>
    <x-loading wire:loading />
    <x-card>
        @if ($negocioSeleccionado)
            <x-flex class="justify-between mb-4">
                <div>
                    <x-h1>Gestión de Productos</x-h1>
                    <flux:heading>Negocio: {{ $negocioSeleccionado->nombre_legal }}</flux:heading>
                </div>
                <div class="flex space-x-2">
                    <flux:button wire:click="cambiarNegocio"
                        variant="filled">
                        Cambiar Negocio
                    </flux:button>
                    <flux:button variant="primary" wire:click="create" icon="plus">
                        Nuevo Producto
                    </flux:button>
                </div>
            </x-flex>

            @if ($isOpen)
                @include('livewire.dueno_tienda.productos.form-producto')
            @else
                @include('livewire.dueno_tienda.productos.lista-productos')
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
