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
            
            <x-table.th class="text-center">Acciones</x-table.th>
            <x-table.th class="text-center">Imagen</x-table.th>
            <x-table.th>Nombre</x-table.th>
            <x-table.th class="text-right">Precio Venta</x-table.th>
            <x-table.th class="text-right">% IGV</x-table.th>
            <x-table.th class="text-center">Estado</x-table.th>
            <x-table.th class="text-center">Stock</x-table.th>
        </x-slot>

        <x-slot name="tbody">
            @forelse($productos as $producto)
                <x-table.tr>
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
                    <x-table.td class="text-center">
                        @if ($producto->imagen_path)
                            <img src="{{ Storage::disk('public')->url($producto->imagen_path) }}"
                                alt="{{ $producto->descripcion }}" class="h-10 w-10 object-cover rounded">
                        @else
                            <x-imagen-empty class="m-auto block" />
                        @endif
                    </x-table.td>
                    <x-table.td>
                        <p>{{ $producto->descripcion }}</p>
                        @if ($producto->detalle)
                            <p>Detalle: {{Str::limit($producto->detalle, 30)}}</p>
                        @endif
                        @if ($producto->codigo_barra)
                            <p>Código de barra: {{$producto->codigo_barra}}</p>
                        @endif
                        @if ($producto->marca)
                            <p>Marca: {{$producto->marca->descripcion_marca}}</p>
                        @endif
                        @if ($producto->categoria)
                            <p>Categoría: {{$producto->categoria->descripcion}}</p>
                        @endif
                        @if ($producto->sunat_code)
                            <p>Cod. Sunat: {{$producto->sunat_code}}</p>
                        @endif
                    </x-table.td>
                    <x-table.td class="text-right">{{ number_format($producto->monto_venta, 2) }}</x-table.td>
                    <x-table.td class="text-right">{{ $producto->porcentaje_igv ?? '-' }}</x-table.td>
                    <x-table.td class="text-center">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $producto->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </x-table.td>
                    <x-table.td class="text-center">
                        {{ $producto->stocks->sum('cantidad') }}
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
