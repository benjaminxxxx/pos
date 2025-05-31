<div class="mb-4">
    <flux:button wire:click="addPresentacion" icon="plus" label="Agregar Presentación" />
</div>

<table class="w-full text-sm table-auto border border-gray-200">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2">Código de Barras</th>
            <th class="p-2">Unidad</th>
            <th class="p-2">Descripción</th>
            <th class="p-2">Factor</th>
            <th class="p-2">Precio</th>
            <th class="p-2 text-center">Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($presentaciones as $index => $presentacion)
            <tr class="border-t">
                <td class="p-2">
                    <flux:input wire:model="presentaciones.{{ $index }}.codigo_barra" type="text"
                        label="" />
                </td>
                <td class="p-2">
                    <flux:select wire:model="presentaciones.{{ $index }}.unidad" label="">
                        <option value="">-</option>
                        @foreach ($unidades as $unidad)
                            <option value="{{ $unidad->codigo }}">{{ $unidad->descripcion }} ({{ $unidad->alt }})
                            </option>
                        @endforeach
                    </flux:select>
                </td>
                <td class="p-2">
                    <flux:input wire:model="presentaciones.{{ $index }}.descripcion" type="text"
                        label="" />
                </td>
                <td class="p-2">
                    <flux:input wire:model="presentaciones.{{ $index }}.factor" type="number" step="0.01"
                        label="" />
                </td>
                <td class="p-2">
                    <flux:input wire:model="presentaciones.{{ $index }}.precio" type="number" step="0.01"
                        label="" />
                </td>
                <td class="p-2 text-center">
                    <flux:button wire:click="removePresentacion({{ $index }})" icon="trash" variant="danger"
                        size="sm" label="Eliminar" />
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


@if (count($presentaciones) === 0)
    <flux:alert variant="warning">
        No hay presentaciones registradas. Si no agrega presentaciones, el producto se venderá por unidad.
    </flux:alert>
@endif
