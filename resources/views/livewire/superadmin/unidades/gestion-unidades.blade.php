<div>
    <x-loading wire:loading />
    <x-card>
        <x-flex class="justify-between mb-4">
            <x-h2>
                Gestión de Unidades
            </x-h2>
            <flux:button variant="primary" wire:click="crear" icon="plus">
                Nueva Unidad
            </flux:button>
        </x-flex>

        @if ($isOpen)
            @include('livewire.superadmin.unidades.form-unidad')
        @endif

        @include('livewire.superadmin.unidades.lista-unidades')
    </x-card>
</div>
