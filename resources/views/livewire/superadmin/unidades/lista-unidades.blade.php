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
            <x-table.th>ID</x-table.th>
            <x-table.th>Nombre</x-table.th>
            <x-table.th>Abreviatura</x-table.th>
            <x-table.th>Tipo de Negocio</x-table.th>
            <x-table.th>Presentaciones</x-table.th>
            <x-table.th>Acciones</x-table.th>
        </x-slot>

        <x-slot name="tbody">
            @forelse($unidades as $unidad)
                <x-table.tr>
                    <x-table.td>{{ $unidad->id }}</x-table.td>
                    <x-table.td>{{ $unidad->nombre }}</x-table.td>
                    <x-table.td>{{ $unidad->abreviatura }}</x-table.td>
                    <x-table.td>{{ ucfirst($unidad->tipo_negocio) }}</x-table.td>
                    <x-table.td>{{ $unidad->presentaciones_count }}</x-table.td>
                    <x-table.td>
                        <x-flex class="justify-center">
                            <flux:button wire:click="editar('{{ $unidad->uuid }}')" icon="pencil" size="sm">
                                Editar
                            </flux:button>
                            <flux:button variant="danger" wire:click="eliminar('{{ $unidad->uuid }}')"
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
        {{ $unidades->links() }}
    </div>
</div>

