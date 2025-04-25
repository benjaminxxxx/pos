<div>
    <x-card>
        <x-flex class="justify-between mb-4">
            <x-h2>
                Gestión de Marcas
            </x-h2>
            <flux:button variant="primary" wire:click="crear" icon="plus">
                Nueva Marca
            </flux:button>
        </x-flex >

        @if($isOpen)
            @include('livewire.superadmin.marcas.form-marca')
        @endif

        @include('livewire.superadmin.marcas.lista-marcas')
    </x-card>
</div>

