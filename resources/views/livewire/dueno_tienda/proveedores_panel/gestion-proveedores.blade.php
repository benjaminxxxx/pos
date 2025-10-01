<div class="space-y-4">


    {{-- Card principal --}}
    <x-card>
        {{-- Encabezado --}}
        <x-flex class="justify-between">
            <div>
                <x-heading size="lg">Gestión de Proveedores</x-heading>
                <x-text class="text-gray-500 text-sm">
                    Administra los proveedores registrados en tus negocios y sucursales.
                </x-text>
            </div>
            <div>
                <x-button @click="$wire.dispatch('registrarProveedor')">
                    <i class="fa fa-plus mr-2"></i> Nuevo Proveedor
                </x-button>
            </div>
        </x-flex>
        {{-- Filtros --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols gap-4 mt-5">


            <x-input placeholder="Buscar por nombre o razón social..." wire:model.live.debounce.500ms="filtroNombre">
                <x-slot name="prepend">
                    <i class="fa fa-search text-gray-400"></i>
                </x-slot>
            </x-input>

            <x-select wire:model.live="filtroEstado">
                <option value="">-- Todos los Estados --</option>
                <option value="ACTIVO">Activo</option>
                <option value="INACTIVO">Inactivo</option>
            </x-select>

            <x-select wire:model.live="filtroEliminados">
                <option value="">-- Todos --</option>
                <option value="Eliminados">Eliminados</option>
            </x-select>
        </div>

        {{-- Tabla --}}
        <x-table class="mt-5">
            <x-slot name="thead">
                <x-th>Documento</x-th>
                <x-th>Razón Social</x-th>
                <x-th>Nombre Comercial</x-th>
                <x-th>Teléfono</x-th>
                <x-th>Email</x-th>
                <x-th>Estado</x-th>
                <x-th class="text-center">Acciones</x-th>
            </x-slot>

            <x-slot name="tbody">
                @forelse($proveedores as $proveedor)
                    <x-tr>
                        <x-td>{{ $proveedor->tipoDocumento->nombre_corto }} - {{ $proveedor->documento_numero }}</x-td>
                        <x-td>{{ $proveedor->razon_social }}</x-td>
                        <x-td>{{ $proveedor->nombre_comercial ?? '-' }}</x-td>
                        <x-td>{{ $proveedor->telefono ?? '-' }}</x-td>
                        <x-td>{{ $proveedor->email ?? '-' }}</x-td>
                        <x-td>
                            <x-badge :color="$proveedor->estado === 'ACTIVO' ? 'green' : 'red'">
                                {{ $proveedor->estado }}
                            </x-badge>
                        </x-td>
                        <x-td class="text-center space-x-2">
                            <x-flex>
                                @if ($proveedor->deleted_at)
                                    <x-button icon="fa fa-undo" variant="success"
                                        wire:click="restaurarProveedor('{{ $proveedor->uuid }}')" />

                                @else
                                    <x-button icon="fa fa-edit"
                                        @click="$wire.dispatch('editarProveedor',{uuid:'{{ $proveedor->uuid }}'})" />
                                    <x-button icon="fa fa-trash" variant="danger"
                                        wire:click="eliminarProveedor('{{ $proveedor->uuid }}')" />
                                @endif
                            </x-flex>

                        </x-td>
                    </x-tr>
                @empty
                    <x-tr>
                        <x-td colspan="7" class="text-center text-gray-500">
                            No se encontraron proveedores.
                        </x-td>
                    </x-tr>
                @endforelse
            </x-slot>
        </x-table>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $proveedores->links() }}
        </div>
    </x-card>


    {{-- Loading spinner --}}
    <x-loading wire:loading />
</div>