<x-card>
    @if (!$negocios || $negocios->isEmpty())
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

                    <x-table.th class="text-center">Acciones</x-table.th>
                    <x-table.th>Nombre Legal</x-table.th>
                    <x-table.th>Nombre Comercial</x-table.th>
                    <x-table.th>Dirección</x-table.th>
                    <x-table.th class="text-center">Logo factura</x-table.th>
                    <x-table.th class="text-center">Certificado</x-table.th>
                    <x-table.th class="text-center">Información factura</x-table.th>
                </x-table.tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($negocios as $item)
                    <x-table.tr>
                        <x-table.td class="text-center">
                            <div class="flex space-x-2 justify-center">
                                <flux:button title="Editar" wire:click="edit('{{ $item->uuid }}')" variant="outline" icon="pencil" size="sm">
                                    
                                </flux:button>
                                <flux:button title="Eliminar" wire:click="eliminarNegocio('{{ $item->uuid }}')"
                                    wire:confirm="¿Está seguro de eliminar este negocio?" variant="danger" size="sm"
                                    icon="trash">
                                    
                                </flux:button>
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->nombre_legal }}
                                <p>{{ $item->ruc }}</p>
                            </div>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->modo === 'produccion' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $item->modo }}
                            </span>
                        </x-table.td>
                        <x-table.td>
                            {{ $item->nombre_comercial }}
                            @if ($item->ubigeo)
                                <p class="text-xs text-gray-500">Ubigeo: {{ $item->ubigeo }}</p>
                            @endif
                            @if ($item->departamento)
                                <p class="text-xs text-gray-500">Departamento: {{ $item->departamento }}</p>
                            @endif
                            @if ($item->provincia)
                                <p class="text-xs text-gray-500">Provincia: {{ $item->provincia }}</p>
                            @endif
                            @if ($item->distrito)
                                <p class="text-xs text-gray-500">Distrito: {{ $item->distrito }}</p>
                            @endif
                            @if ($item->urbanizacion)
                                <p class="text-xs text-gray-500">Urbanización: {{ $item->urbanizacion }}</p>
                            @endif
                            @if ($item->codigo_pais)
                                <p class="text-xs text-gray-500">Código País: {{ $item->codigo_pais }}</p>
                            @endif
                        </x-table.td>
                        
                        <x-table.td>
                            {{ $item->direccion }}
                        </x-table.td>
                        <x-table.td class="text-center">
                            @if ($item->logo_factura)
                                <img src="{{ Storage::disk('public')->url($item->logo_factura) }}" alt="Logo Factura"
                                    class="w-14 h-14 object-cover rounded-lg shadow m-auto">
                            @else
                                <p class="text-sm">Sin Logo</p>
                            @endif
                        </x-table.td>
                        <x-table.td class="text-center">
                            @if ($item->certificado)
                                <img src="{{ asset('image/pemfile.png') }}" alt="Logo Factura"
                                    class="w-16 h-16 object-cover rounded-lg shadow m-auto" />
                                <br>
                                <x-a href="{{ Storage::disk('public')->url($item->certificado) }}" download target="_blank"
                                    class="text-sm">
                                    Descargar Certificado
                                </x-a>
                            @else
                                Sin Certificado
                            @endif
                        </x-table.td>
                        <x-table.td class="text-left">
                            @php
                                $agrupado = $item->informacionAdicional->groupBy('ubicacion');
                            @endphp

                            @foreach (['Cabecera', 'Centro', 'Pie'] as $seccion)
                                @if ($agrupado->has($seccion))
                                    <div class="mb-2">
                                        <div class="font-semibold text-gray-800">{{ $seccion }}</div>
                                        <ul class="list-disc list-inside text-sm text-gray-700 ml-2">
                                            @foreach ($agrupado[$seccion] as $info)
                                                <li>{{ $info->clave }}: {{ $info->valor }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endforeach
                        </x-table.td>
                    </x-table.tr>
                @endforeach
            </x-slot>
        </x-table>
    @endif
</x-card>