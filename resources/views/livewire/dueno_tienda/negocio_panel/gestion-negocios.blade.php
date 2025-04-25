<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <flux:heading>Gestión de Negocios</flux:heading>
        <flux:button wire:click="create" variant="primary" icon="plus">
            Nuevo Negocio
        </flux:button>
    </div>

    @if($showForm)
        <x-card>
            @if($isEditing)
                <flux:heading size="lg" class="mb-6">Editar Negocio: {{ $nombre_legal }}</flux:heading>
            @else
                <flux:heading size="lg" class="mb-6">Nuevo Negocio</flux:heading>
            @endif

            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button 
                            wire:click="setActiveTab('general')" 
                            class="py-4 px-1 {{ $activeTab === 'general' ? 'border-b-2 border-primary-500 text-primary-600' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm"
                        >
                            Información General
                        </button>
                        <button 
                            wire:click="setActiveTab('info_adicional')" 
                            class="py-4 px-1 {{ $activeTab === 'info_adicional' ? 'border-b-2 border-primary-500 text-primary-600' : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm"
                        >
                            Información Adicional
                        </button>
                    </nav>
                </div>
            </div>

            @if($activeTab === 'general')
                @include('livewire.dueno_tienda.negocio_panel.form-negocio')
            @elseif($activeTab === 'info_adicional')
                @include('livewire.dueno_tienda.negocio_panel.form-info-adicional')
            @endif

            <x-flex-end class="mt-6">
                <flux:button wire:click="cancel" variant="outline" type="button">
                    Cancelar
                </flux:button>
                <flux:button wire:click="save" variant="primary">
                    {{ $isEditing ? 'Actualizar' : 'Guardar' }}
                </flux:button>
            </x-flex-end>
        </x-card>
    @else
        @include('livewire.dueno_tienda.negocio_panel.lista-negocios')
    @endif
</div>

