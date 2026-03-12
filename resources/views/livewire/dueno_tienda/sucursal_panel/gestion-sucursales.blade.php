<div class="space-y-4">
    <x-flex class="justify-between">
        <x-title>
            Gestión de Sucursales
        </x-title>
        <flux:button wire:click="create" icon="plus">
            Nueva Sucursal
        </flux:button>
    </x-flex>

    @if ($showForm)
        @include('livewire.dueno_tienda.sucursal_panel.form-sucursal')
    @else
        @include('livewire.dueno_tienda.sucursal_panel.lista-sucursales')
    @endif
</div>
