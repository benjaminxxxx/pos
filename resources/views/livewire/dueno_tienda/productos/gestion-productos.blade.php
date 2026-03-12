<div class="space-y-4">
    <x-flex class="justify-between">
        <x-title>Gestión de Productos</x-title>
        <flux:button wire:click="create" icon="plus">
            Nuevo Producto
        </flux:button>
    </x-flex>
    <x-card>
        @if ($negocio)

            @if ($isOpen)
                @include('livewire.dueno_tienda.productos.form-producto')
            @else
                @include('livewire.dueno_tienda.productos.lista-productos')
            @endif
        @else
            <x-flex class="flex-col justify-center h-64">
                <x-h2 class="mb-4">No hay negocio seleccionado</x-h2>
            </x-flex>
        @endif
    </x-card>
    <x-loading wire:loading />
</div>
