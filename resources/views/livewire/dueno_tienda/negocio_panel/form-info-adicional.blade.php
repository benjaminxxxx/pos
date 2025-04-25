<div class="space-y-8">
    <!-- Formulario para agregar nueva información -->
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
        <flux:heading size="sm" class="mb-4">Agregar Información Adicional</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <flux:input wire:model="nuevaClave" label="Nombre" placeholder="Ej: Celular, Cuenta BCP, etc." />
            </div>
            <div>
                <flux:input wire:model="nuevoValor" label="Valor" placeholder="Ej: 999999999, 123456789, etc." />
            </div>
            <div>
                <flux:select wire:model="nuevaUbicacion" label="Ubicación">
                    <option value="Cabecera">Cabecera</option>
                    <option value="Centro">Información Adicional</option>
                    <option value="Pie">Pie de Página</option>
                </flux:select>
            </div>
        </div>
        <div class="mt-4">
            <flux:button wire:click="agregarInformacionAdicional" variant="primary" size="sm" icon="plus">
                Agregar
            </flux:button>
        </div>
    </div>

    <!-- Sección Cabecera -->
    <div>
        <flux:heading size="sm" class="mb-4">Cabecera</flux:heading>
        @if(count($infoAdicionalCabecera) > 0)
            <x-table responsive>
                <x-slot name="thead">
                    <x-table.tr>
                        <x-table.th>Nombre</x-table.th>
                        <x-table.th>Valor</x-table.th>
                        <x-table.th class="text-center">Acciones</x-table.th>
                    </x-table.tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach($infoAdicionalCabecera as $index => $item)
                        <x-table.tr>
                            <x-table.td>{{ $item['clave'] }}</x-table.td>
                            <x-table.td>{{ $item['valor'] }}</x-table.td>
                            <x-table.td class="text-center">
                                <flux:button 
                                    wire:click="eliminarInformacionAdicional('Cabecera', {{ $index }})" 
                                    variant="danger" 
                                    size="sm"
                                    icon="trash"
                                >
                                    Eliminar
                                </flux:button>
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                </x-slot>
            </x-table>
        @else
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-gray-500 dark:text-gray-400">No hay información en la cabecera</p>
            </div>
        @endif
    </div>

    <!-- Sección Información Adicional -->
    <div>
        <flux:heading size="sm" class="mb-4">Información Adicional</flux:heading>
        @if(count($infoAdicionalCentro) > 0)
            <x-table responsive>
                <x-slot name="thead">
                    <x-table.tr>
                        <x-table.th>Nombre</x-table.th>
                        <x-table.th>Valor</x-table.th>
                        <x-table.th class="text-center">Acciones</x-table.th>
                    </x-table.tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach($infoAdicionalCentro as $index => $item)
                        <x-table.tr>
                            <x-table.td>{{ $item['clave'] }}</x-table.td>
                            <x-table.td>{{ $item['valor'] }}</x-table.td>
                            <x-table.td class="text-center">
                                <flux:button 
                                    wire:click="eliminarInformacionAdicional('Centro', {{ $index }})" 
                                    variant="danger" 
                                    size="sm"
                                    icon="trash"
                                >
                                    Eliminar
                                </flux:button>
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                </x-slot>
            </x-table>
        @else
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-gray-500 dark:text-gray-400">No hay información adicional</p>
            </div>
        @endif
    </div>

    <!-- Sección Pie de Página -->
    <div>
        <flux:heading size="sm" class="mb-4">Pie de Página</flux:heading>
        @if(count($infoAdicionalPie) > 0)
            <x-table responsive>
                <x-slot name="thead">
                    <x-table.tr>
                        <x-table.th>Nombre</x-table.th>
                        <x-table.th>Valor</x-table.th>
                        <x-table.th class="text-center">Acciones</x-table.th>
                    </x-table.tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach($infoAdicionalPie as $index => $item)
                        <x-table.tr>
                            <x-table.td>{{ $item['clave'] }}</x-table.td>
                            <x-table.td>{{ $item['valor'] }}</x-table.td>
                            <x-table.td class="text-center">
                                <flux:button 
                                    wire:click="eliminarInformacionAdicional('Pie', {{ $index }})" 
                                    variant="danger" 
                                    size="sm"
                                    icon="trash"
                                >
                                    Eliminar
                                </flux:button>
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                </x-slot>
            </x-table>
        @else
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-gray-500 dark:text-gray-400">No hay información en el pie de página</p>
            </div>
        @endif
    </div>
</div>

