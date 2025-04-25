<div>
    <div class="flex flex-col md:flex-row justify-between mb-4 gap-4">
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
            placeholder="Buscar servicios..." />
        </div>
        
        <div class="flex flex-col md:flex-row gap-2">
                      
            <flux:select wire:model.live="categoriaFilter" label="Categoría" class="w-full">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->descripcion }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="sucursalFilter" label="Sucursal" class="w-full">
                <option value="">Todas las sucursales</option>
                @foreach ($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
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
            <x-table.th>Código</x-table.th>
            <x-table.th>Nombre</x-table.th>
            <x-table.th>Categoría</x-table.th>
            <x-table.th>Sucursal</x-table.th>
            <x-table.th class="text-center">Precio</x-table.th>
            <x-table.th class="text-center">Estado</x-table.th>
            <x-table.th class="text-center">Acciones</x-table.th>
        </x-slot>
        
        <x-slot name="tbody">
            @forelse($servicios as $servicio)
                <x-table.tr>
                    <x-table.td>{{ $servicio->codigo }}</x-table.td>
                    <x-table.td>{{ $servicio->nombre }}</x-table.td>
                    <x-table.td>{{ $servicio->categoria ? $servicio->categoria->descripcion : 'N/A' }}</x-table.td>
                    <x-table.td class="text-center">{{ $servicio->sucursal ? $servicio->sucursal->nombre : 'N/A' }}</x-table.td>
                    <x-table.td class="text-center">{{ number_format($servicio->precio, 2) }}</x-table.td>
                    <x-table.td class="text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $servicio->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $servicio->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </x-table.td>
                    <x-table.td>
                        <x-flex class="justify-center">
                        
                            <flux:button wire:click="editar('{{ $servicio->uuid }}')" icon="pencil" size="sm">
                                Editar
                            </flux:button>

                            <flux:button variant="danger" wire:click="eliminar('{{$servicio->uuid}}')" wire:confirm="¿Está seguro de eliminar este registro?"
                                icon="trash" size="sm">
                                Eliminar
                            </flux:button>

                        </x-flex>
                    </x-table.td>
                </x-table.tr>
            @empty
            <x-table.empty colspan="8" />
            @endforelse
        </x-slot>
    </x-table>

    <div class="mt-4">
        {{ $servicios->links() }}
    </div>
</div>
