<div class="mt-4">
    <div class="flex justify-between mb-4">
        <div class="w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar unidades..." />
        </div>
    </div>

    <x-table>
        <x-slot name="thead">
            <x-table.th>Código</x-table.th>
            <x-table.th>Descripción</x-table.th>
            <x-table.th>Acciones</x-table.th>
        </x-slot>

        <x-slot name="tbody">
            @forelse($unidades as $unidad)
                <x-table.tr>
                    <x-table.td>{{ $unidad->codigo }}</x-table.td>
                    <x-table.td>{{ $unidad->descripcion }}</x-table.td>
                    <x-table.td>
                        <x-flex class="justify-center">
                            <flux:button wire:click="editar('{{ $unidad->codigo }}')" icon="pencil" size="sm">
                                Editar
                            </flux:button>
                            <flux:button variant="danger" wire:click="eliminar('{{ $unidad->codigo }}')"
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

