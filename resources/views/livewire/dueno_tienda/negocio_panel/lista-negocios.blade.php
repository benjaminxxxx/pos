<x-card>
    @if(!$negocios || $negocios->isEmpty())
        <div class="p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">No hay negocios registrados.</p>
            <flux:button wire:click="create" type="link" class="mt-2">
                Agregar un negocio
            </flux:button>
        </div>
    @else
        <x-table responsive>
            <x-slot name="thead">
                <x-table.tr>
                    <x-table.th>Nombre Legal</x-table.th>
                    <x-table.th>RUC</x-table.th>
                    <x-table.th>Dirección</x-table.th>
                    <x-table.th>Modo</x-table.th>
                    <x-table.th class="text-center">Acciones</x-table.th>
                </x-table.tr>
            </x-slot>
            
            <x-slot name="tbody">
                @foreach($negocios as $item)
                    <x-table.tr>
                        <x-table.td>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->nombre_legal }}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            {{ $item->ruc }}
                        </x-table.td>
                        <x-table.td>
                            {{ $item->direccion }}
                        </x-table.td>
                        <x-table.td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->modo === 'produccion' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $item->modo }}
                            </span>
                        </x-table.td>
                        <x-table.td class="text-center">
                            <div class="flex space-x-2 justify-center">
                                <flux:button wire:click="edit('{{ $item->uuid }}')" variant="outline" icon="pencil" size="sm">
                                    Editar
                                </flux:button>
                                <flux:button 
                                    wire:click="delete('{{ $item->uuid }}')" 
                                    wire:confirm="¿Está seguro de eliminar este negocio?"
                                    variant="danger" 
                                    size="sm"
                                    icon="trash"
                                >
                                Eliminar
                                </flux:button>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                @endforeach
            </x-slot>
        </x-table>
    @endif
</x-card>

