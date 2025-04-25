<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading>Gestión de Sucursales</flux:heading>
        <flux:button wire:click="create" variant="primary" icon="plus">
            Nueva Sucursal
        </flux:button>
    </div>

    @if($showForm)
        @include('livewire.dueno_tienda.sucursal_panel.form-sucursal')
    @else
        @include('livewire.dueno_tienda.sucursal_panel.lista-sucursales')
    @endif
</div>

