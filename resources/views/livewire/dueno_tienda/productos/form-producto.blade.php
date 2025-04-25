<div>
    <div class="mb-4">
        <flux:button wire:click="closeModal" icon="arrow-left">
            Volver
        </flux:button>
    </div>

    <form wire:submit.prevent="store">
        @php
            $tabs = [
                'general' => 'Información General',
                'imagen' => 'Imagen',
                'presentaciones' => 'Presentaciones',
                'stock' => 'Stock',
            ];
        @endphp

        <x-tabs :tabs="$tabs">
            <x-tabs.tab name="general">
                @include('livewire.dueno_tienda.productos.partials.form-general')
            </x-tabs.tab>

            <x-tabs.tab name="imagen">
                @include('livewire.dueno_tienda.productos.partials.form-imagen')
            </x-tabs.tab>

            <x-tabs.tab name="presentaciones">
                @include('livewire.dueno_tienda.productos.partials.form-presentaciones')                
            </x-tabs.tab>

            <x-tabs.tab name="stock">
                @include('livewire.dueno_tienda.productos.partials.form-stock')
            </x-tabs.tab>
        </x-tabs>
        <div class="mt-6 flex justify-end">
            <flux:button type="button" wire:click="closeModal" class="mr-2">
                Cancelar
            </flux:button>
            <flux:button variant="primary" type="submit">
                {{ $producto_id ? 'Actualizar' : 'Guardar' }}
            </flux:button>
        </div>
    </form>
</div>
