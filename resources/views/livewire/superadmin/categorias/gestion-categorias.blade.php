<div>
    <x-loading wire:loading />
    <x-card>
        <x-flex class="justify-between mb-4">
            <x-h2>
                Gestión de Categorías
            </x-h2>
            <flux:button variant="primary" wire:click="crear" icon="plus">
                Nueva Categoría
            </flux:button>
        </x-flex>

        @if ($isOpen)
            @include('livewire.superadmin.categorias.form-categoria')
        @endif

        @include('livewire.superadmin.categorias.lista-categorias')
    </x-card>
</div>
