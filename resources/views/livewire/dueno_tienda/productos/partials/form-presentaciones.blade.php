<div class="mb-4">
    <flux:button wire:click="addPresentacion" icon="plus" label="Agregar Presentación" />
</div>

@foreach ($presentaciones as $index => $presentacion)
<flux:card title="Presentación #{{ $index + 1 }}">
    <div class="flex justify-end">
        <flux:button wire:click="removePresentacion({{ $index }})" icon="trash" variant="danger" size="sm" label="Eliminar" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <flux:input wire:model="presentaciones.{{ $index }}.codigo_barra" label="Código de Barras" type="text" />
        
        <flux:select wire:model="presentaciones.{{ $index }}.unidad_id" label="Unidad">
            <option value="">Seleccione una unidad</option>
            @foreach ($unidades as $unidad)
                <option value="{{ $unidad->id }}">{{ $unidad->nombre }} ({{ $unidad->abreviatura }})</option>
            @endforeach
        </flux:select>

        <flux:input wire:model="presentaciones.{{ $index }}.descripcion" label="Descripción" type="text" />
        <flux:input wire:model="presentaciones.{{ $index }}.factor" label="Factor" type="number" step="0.01" />
        <flux:input wire:model="presentaciones.{{ $index }}.precio" label="Precio" type="number" step="0.01" />
    </div>
</flux:card>

@endforeach

@if (count($presentaciones) === 0)
    <flux:alert variant="warning">
        No hay presentaciones registradas. Si no agrega presentaciones, el producto se venderá por unidad.
    </flux:alert>
@endif