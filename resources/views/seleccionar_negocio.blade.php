<div>
    <x-flex class="flex-col justify-center h-64">
        <x-h2 class="mb-4">No hay negocio seleccionado</x-h2>
        <flux:button wire:click="cambiarNegocio" variant="primary">
            Seleccionar Negocio
        </flux:button>
    </x-flex>
    <!-- Modal para seleccionar negocio -->
    <x-seleccionar-negocio-modal :mostrar="$mostrarModalSeleccionNegocio" :negocios="$negocios" />
</div>
