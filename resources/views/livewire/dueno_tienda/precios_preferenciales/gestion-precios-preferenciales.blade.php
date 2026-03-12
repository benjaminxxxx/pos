<div class="space-y-4">
    <x-flex class="justify-between">
        <x-title>Gestión de Productos</x-title>
        <flux:button wire:click="create" icon="plus">
            Nuevo Producto
        </flux:button>
    </x-flex>
    <x-card>
        @if ($negocioSeleccionado)
            <x-flex class="justify-between mb-4">
                <div>

                </div>
                <div class="flex space-x-2">
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
    <x-loading wire:loading />
</div>
