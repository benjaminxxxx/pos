<x-card>
    <flux:heading class="mb-6">{{ $isEditing ? 'Editar Sucursal' : 'Nueva Sucursal' }} para {{ $negocioSeleccionado->nombre_legal }}</flux:heading>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="nombre" label="Nombre de Sucursal" placeholder="Nombre de la sucursal" required />
            <flux:input wire:model="direccion" label="Dirección" placeholder="Dirección de la sucursal" required />
            <flux:input wire:model="telefono" label="Teléfono" placeholder="Teléfono de contacto" />
            <flux:input wire:model="email" label="Email" type="email" placeholder="Email de contacto" />
            <flux:checkbox wire:model="es_principal" label="Es sucursal principal" />
            <flux:checkbox wire:model="estado" label="Activo" />
        </div>

        <x-flex-end>
            <flux:button wire:click="cancel" variant="outline" type="button">
                Cancelar
            </flux:button>
            <flux:button type="submit" variant="primary">
                {{ $isEditing ? 'Actualizar' : 'Guardar' }}
            </flux:button>
        </x-flex-end>
    </form>
</x-card>
