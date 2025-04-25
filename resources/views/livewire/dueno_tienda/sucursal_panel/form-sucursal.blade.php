<x-card>
    <flux:heading size="lg" class="mb-6">{{ $isEditing ? 'Editar Sucursal' : 'Nueva Sucursal' }}</flux:heading>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:select wire:model="negocio_id" label="Negocio" required>
                <option value="">Seleccione un negocio</option>
                @foreach ($negocios as $negocio)
                    <option value="{{ $negocio->id }}">{{ $negocio->nombre_legal }}</option>
                @endforeach
            </flux:select>
            <flux:input wire:model="nombre" label="Nombre" placeholder="Nombre de la sucursal" required />
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
