<div>

    <div class="flex flex-col md:flex-row justify-between mb-4 gap-4">
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                placeholder="Buscar productos..." />
        </div>

        <div class="flex flex-col md:flex-row gap-2">
            <flux:select wire:model.live="categoriaFilter" label="Categoría" class="w-full">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->descripcion }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="marcaFilter" label="Marca" class="w-full">
                <option value="">Todas las marcas</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->descripcion_marca }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="activoFilter" label="Estado" class="w-full">
                <option value="">Todos los estados</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </flux:select>

        </div>
    </div>

    <x-table>
        <x-slot name="thead">
            <x-table.th>Código de Barras</x-table.th>
            <x-table.th>Imagen</x-table.th>
            <x-table.th>Nombre</x-table.th>
            <x-table.th>Descripción</x-table.th>
            <x-table.th>Categoría</x-table.th>
            <x-table.th>Marca</x-table.th>
            <x-table.th>Código SUNAT</x-table.th>
            <x-table.th>Precio Venta</x-table.th>
            <x-table.th>% IGV</x-table.th>
            <x-table.th>Estado</x-table.th>
            <x-table.th>Acciones</x-table.th>
        </x-slot>

        <x-slot name="tbody">
            @forelse($productos as $producto)
                <x-table.tr>
                    <x-table.td>{{ $producto->codigo_barra }}</x-table.td>
                    <x-table.td>
                        @if ($producto->imagen_path)
                            <img src="{{ Storage::disk('public')->url($producto->imagen_path) }}"
                                alt="{{ $producto->descripcion }}" class="h-10 w-10 object-cover rounded">
                        @else
                            <x-imagen-empty />
                        @endif
                    </x-table.td>
                    <x-table.td>{{ $producto->descripcion }}</x-table.td>
                    <x-table.td>{{ Str::limit($producto->detalle, 30) }}</x-table.td>
                    <x-table.td>{{ $producto->categoria ? $producto->categoria->descripcion : 'N/A' }}</x-table.td>
                    <x-table.td>{{ $producto->marca ? $producto->marca->descripcion_marca : 'N/A' }}</x-table.td>
                    <x-table.td>{{ $producto->sunat_code ?? 'N/A' }}</x-table.td>
                    <x-table.td>{{ number_format($producto->monto_venta, 2) }}</x-table.td>
                    <x-table.td>{{ $producto->porcentaje_igv ?? '-' }}</x-table.td>
                    <x-table.td>
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </x-table.td>
                    <x-table.td>
                        <div class="flex space-x-2">
                            <flux:button wire:click="edit('{{ $producto->uuid }}')" icon="pencil" size="sm">
                                Editar
                            </flux:button>

                            <flux:button variant="danger" wire:click="delete('{{ $producto->uuid }}')"
                                wire:confirm="¿Está seguro de eliminar este registro?" icon="trash" size="sm">
                                Eliminar
                            </flux:button>
                        </div>
                    </x-table.td>
                </x-table.tr>
            @empty
                <x-table.empty colspan="11" />
            @endforelse
        </x-slot>
    </x-table>


    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
