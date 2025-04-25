<div class="mt-4">
    <div class="flex justify-between mb-4">
        <div class="w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar unidades..." />
        </div>
        <div>
            <flux:select wire:model.live="tipoNegocioFilter" label="Tipo de negocio" class="w-full">
                <option value="">Todos los tipos</option>
                @foreach (config('negocios.tipos') as $valor => $nombre)
                    <option value="{{ $valor }}">{{ $nombre }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <x-table>
        <x-slot name="thead">
            <x-table.th class="text-center">ID</x-table.th>
            <x-table.th class="text-left">Descripción</x-table.th>
            <x-table.th class="text-center">Tipo de Negocio</x-table.th>
            <x-table.th class="text-center">Categoría</x-table.th>
            <x-table.th class="text-center">Productos</x-table.th>
            <x-table.th class="text-center">Acciones</x-table.th>
        </x-slot>

        <x-slot name="tbody">
            @forelse($marcas as $marca)
                <x-table.tr>
                    <x-table.td class="text-center">{{ $marca->id }}</x-table.td>
                    <x-table.td>{{ $marca->descripcion_marca }}</x-table.td>
                    <x-table.td class="text-center">{{ ucfirst($marca->tipo_negocio) }}</x-table.td>
                    <x-table.td class="text-center">{{ $marca->categoria ? $marca->categoria->descripcion : 'N/A' }}</x-table.td>
                    <x-table.td class="text-center">{{ $marca->productos_count }}</x-table.td>
                    <x-table.td class="text-center">
                        <x-flex class="justify-center">
                            <flux:button wire:click="editar('{{ $marca->uuid }}')" icon="pencil" size="sm">
                                Editar
                            </flux:button>
                            <flux:button variant="danger" wire:click="eliminar('{{ $marca->uuid }}')"
                                wire:confirm="¿Está seguro de eliminar este registro?" icon="trash" size="sm">
                                Eliminar
                            </flux:button>
                        </x-flex>
                    </x-table.td>
                </x-table.tr>
            @empty
                <x-table.empty colspan="100%" />
            @endforelse
        </x-slot>
    </x-table>

    <div class="mt-4">
        {{ $marcas->links() }}
    </div>
</div>
