<div class="space-y-4">

    <x-flex class="justify-between">
        <x-title>
            Gestión de Clientes
        </x-title>
        @if (!$mostrarFormulario)
            <div class="flex space-x-2">
                <flux:button variant="primary" wire:click="crearCliente" icon="plus">
                    Nuevo Cliente
                </flux:button>
            </div>
        @endif
    </x-flex>
    <x-card>
        @if ($mostrarFormulario)
            @include('livewire.dueno_tienda.cliente_panel.form-cliente')
        @else
            @include('livewire.dueno_tienda.cliente_panel.lista-clientes')
        @endif
    </x-card>
    <x-loading wire:loading />
</div>
