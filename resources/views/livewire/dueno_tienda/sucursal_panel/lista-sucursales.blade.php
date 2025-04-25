<x-card>
    @if(!$sucursales || $sucursales->isEmpty())
        <div class="p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">No hay sucursales registradas.</p>
            <flux:button wire:click="create" type="link" class="mt-2">
                Agregar una sucursal
            </flux:button>
        </div>
    @else
        <x-table responsive>
            <x-slot name="thead">
                <x-table.tr>
                    <x-table.th>Negocio</x-table.th>
                    <x-table.th>Nombre</x-table.th>
                    <x-table.th>Dirección</x-table.th>
                    <x-table.th>Teléfono</x-table.th>
                    <x-table.th>Principal</x-table.th>
                    <x-table.th>Estado</x-table.th>
                    <x-table.th class="text-center">Acciones</x-table.th>
                </x-table.tr>
            </x-slot>
            
            <x-slot name="tbody">
                @foreach($sucursales as $item)
                    <x-table.tr>
                        <x-table.td>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->negocio->nombre_legal }}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            {{ $item->nombre }}
                        </x-table.td>
                        <x-table.td>
                            {{ $item->direccion }}
                        </x-table.td>
                        <x-table.td>
                            {{ $item->telefono ?: 'N/A' }}
                        </x-table.td>
                        <x-table.td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->es_principal ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $item->es_principal ? 'Sí' : 'No' }}
                            </span>
                        </x-table.td>
                        <x-table.td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </x-table.td>
                        <x-table.td class="text-center">
                            <div class="flex space-x-2 justify-center">
                                <flux:button wire:click="edit('{{ $item->uuid }}')" variant="outline" icon="pencil" size="sm">
                                    Editar
                                </flux:button>
                                <flux:button 
                                    wire:click="delete('{{ $item->uuid }}')" 
                                    wire:confirm="¿Está seguro de eliminar esta sucursal?"
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

