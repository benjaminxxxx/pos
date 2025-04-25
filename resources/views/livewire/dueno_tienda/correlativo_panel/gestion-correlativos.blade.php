<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading>Gestión de Correlativos</flux:heading>
        <flux:button wire:click="create" variant="primary" icon="plus">
            Nuevo Correlativo
        </flux:button>
    </div>

    @if($showForm)
        @include('livewire.dueno_tienda.correlativo_panel.form-correlativo')
    @else
        @include('livewire.dueno_tienda.correlativo_panel.lista-correlativos')
    @endif
</div>

