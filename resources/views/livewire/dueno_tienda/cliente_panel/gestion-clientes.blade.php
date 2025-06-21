<div>
    <x-loading wire:loading />
    <x-card>
        <x-flex class="justify-between mb-4">
            <div>
                <x-h1>Gestión de Clientes</x-h1>
            </div>
            @if (!$mostrarFormulario)
                <div class="flex space-x-2">
                    <flux:button variant="primary" wire:click="crearCliente" icon="plus">
                        Nuevo Cliente
                    </flux:button>
                </div>
            @endif
        </x-flex>

        @if ($mostrarFormulario)
            @include('livewire.dueno_tienda.cliente_panel.form-cliente')
        @else
            @include('livewire.dueno_tienda.cliente_panel.lista-clientes')
        @endif
    </x-card>
</div>
