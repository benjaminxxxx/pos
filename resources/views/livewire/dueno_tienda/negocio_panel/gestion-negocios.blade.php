<div class="space-y-4">
    <x-flex class="justify-between">
        <flux:heading>Gestión de Negocios</flux:heading>
        <flux:button wire:click="create" icon="plus">
            {{ $isEditing ? 'Editar Negocio' : 'Nuevo Negocio' }}
        </flux:button>
    </x-flex>

    @include('livewire.dueno_tienda.negocio_panel.lista-negocios')

    <x-dialog-modal wire:model="mostrarFormularioNegocio">
        <x-slot name="title">
            Editar Negocio
        </x-slot>
        <x-slot name="content">
            <div x-data="{ tab: 'general' }">

                <div class="mb-6">
                    <nav class="-mb-px flex space-x-8">

                        <button @click="tab='general'"
                            :class="tab === 'general' ?
                                'border-white bg-zinc-300 dark:bg-zinc-600' :
                                'border-border text-zinc-600 dark:text-zinc-300 hover:text-zinc-500 dark:hover:text-zinc-200'"
                            class="py-3 px-2 border-b-2 font-medium text-sm">
                            Información General
                        </button>

                        <button @click="tab='ubicacion'"
                            :class="tab === 'ubicacion' ?
                                'border-white  bg-zinc-300 dark:bg-zinc-600' :
                                'border-border text-zinc-600 dark:text-zinc-300 hover:text-zinc-500 dark:hover:text-zinc-200'"
                            class="py-3 px-2 border-b-2 font-medium text-sm">
                            Ubicación
                        </button>

                        <button @click="tab='archivos'"
                            :class="tab === 'archivos' ?
                                'border-white  bg-zinc-300 dark:bg-zinc-600' :
                                'border-border text-zinc-600 dark:text-zinc-300 hover:text-zinc-500 dark:hover:text-zinc-200'"
                            class="py-3 px-2 border-b-2 font-medium text-sm">
                            Archivos
                        </button>

                        <button @click="tab='info_adicional'"
                            :class="tab === 'info_adicional' ?
                                'border-white  bg-zinc-300 dark:bg-zinc-600' :
                                'border-border text-zinc-600 dark:text-zinc-300 hover:text-zinc-500 dark:hover:text-zinc-200'"
                            class="py-3 px-2 border-b-2 font-medium text-sm ">
                            Información Adicional
                        </button>

                    </nav>
                </div>

                <div x-show="tab==='general'" x-cloak>
                    @include('livewire.dueno_tienda.negocio_panel.form-negocio-general')
                </div>

                <div x-show="tab==='ubicacion'" x-cloak>
                    @include('livewire.dueno_tienda.negocio_panel.form-negocio-ubicacion')
                </div>

                <div x-show="tab==='archivos'" x-cloak>
                    @include('livewire.dueno_tienda.negocio_panel.form-negocio-archivos')
                </div>

                <div x-show="tab==='info_adicional'" x-cloak>
                    @include('livewire.dueno_tienda.negocio_panel.form-info-adicional')
                </div>

            </div>
        </x-slot>
        <x-slot name="footer">
            <flux:button wire:click="cancel" variant="outline" type="button">
                Cancelar
            </flux:button>
            <flux:button wire:click="guardarNegocio" icon="pencil-square" variant="primary">
                {{ $isEditing ? 'Actualizar' : 'Guardar' }}
            </flux:button>
        </x-slot>
    </x-dialog-modal>
    <x-loading wire:loading />
</div>
