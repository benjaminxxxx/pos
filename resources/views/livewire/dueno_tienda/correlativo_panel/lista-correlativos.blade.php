<div>
    @if (!$correlativos || $correlativos->isEmpty())
        <div class="p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">No hay correlativos registrados.</p>
            <flux:button wire:click="create" type="link" class="mt-2">
                Agregar un correlativo
            </flux:button>
        </div>
    @else
        <x-table responsive>
            <x-slot name="thead">
                <x-table.tr>
                    <x-table.th>Tipo</x-table.th>
                    <x-table.th>Serie</x-table.th>
                    <x-table.th>Correlativo Actual</x-table.th>
                    <x-table.th>Sucursales</x-table.th>
                    <x-table.th>Estado</x-table.th>
                    <x-table.th class="text-center">Acciones</x-table.th>
                </x-table.tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($correlativos as $item)
                    <x-table.tr>
                        <x-table.td>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->tipoComprobante->nombre }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Código: {{ $item->tipoComprobante->codigo }}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            {{ $item->serie }}
                        </x-table.td>
                        <x-table.td>
                            {{ $item->correlativo_actual }}
                            <div class="text-xs text-gray-500">
                                Siguiente:
                                {{ $item->serie }}-{{ str_pad($item->correlativo_actual + 1, 8, '0', STR_PAD_LEFT) }}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <div class="text-sm">
                                @foreach ($item->sucursales as $sucursal)
                                    <div class="mb-1">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $sucursal->negocio->nombre_legal }} - {{ $sucursal->nombre }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </x-table.td>
                        <x-table.td class="text-center">
                            <div class="flex space-x-2 justify-center">
                                <flux:button wire:click="edit({{ $item->id }})" variant="outline" icon="pencil"
                                    size="sm">
                                    Editar
                                </flux:button>
                                <flux:button wire:click="delete({{ $item->id }})"
                                    wire:confirm="¿Está seguro de eliminar este correlativo?" variant="danger"
                                    size="sm" icon="trash">
                                    Eliminar
                                </flux:button>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                @endforeach
            </x-slot>
        </x-table>
    @endif
</div>
