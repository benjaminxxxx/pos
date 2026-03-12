<x-card>
    @if (!$negocios || $negocios->isEmpty())
        <div class="p-6 text-center">
            <p>No hay negocios registrados.</p>
            <flux:button wire:click="create" type="link" class="mt-2">
                Agregar un negocio
            </flux:button>
        </div>
    @else
        <x-table responsive>
            <x-slot name="thead">
                <x-tr>

                    <x-th class="text-center">Acciones</x-th>
                    <x-th>Nombre Legal</x-th>
                    <x-th>Nombre Comercial</x-th>
                    <x-th>Dirección</x-th>
                    <x-th class="text-center">Logo factura</x-th>
                    <x-th class="text-center">Certificado</x-th>
                    <x-th class="text-center">Información factura</x-th>
                </x-tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($negocios as $item)
                    <x-tr>
                        <x-td class="text-center">
                            <div class="flex space-x-2 justify-center">
                                <flux:button title="Editar" wire:click="edit('{{ $item->uuid }}')" variant="outline" icon="pencil" size="sm">
                                    
                                </flux:button>
                                <flux:button title="Eliminar" wire:click="eliminarNegocio('{{ $item->uuid }}')"
                                    wire:confirm="¿Está seguro de eliminar este negocio?" variant="danger" size="sm"
                                    icon="trash">
                                    
                                </flux:button>
                            </div>
                        </x-td>
                        <x-td>
                            <div class="text-sm font-medium">
                                {{ $item->nombre_legal }}
                                <p>{{ $item->ruc }}</p>
                            </div>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->modo === 'produccion' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $item->modo }}
                            </span>
                        </x-td>
                        <x-td>
                            {{ $item->nombre_comercial }}
                            @if ($item->ubigeo)
                                <p>Ubigeo: {{ $item->ubigeo }}</p>
                            @endif
                            @if ($item->departamento)
                                <p>Departamento: {{ $item->departamento }}</p>
                            @endif
                            @if ($item->provincia)
                                <p>Provincia: {{ $item->provincia }}</p>
                            @endif
                            @if ($item->distrito)
                                <p>Distrito: {{ $item->distrito }}</p>
                            @endif
                            @if ($item->urbanizacion)
                                <p>Urbanización: {{ $item->urbanizacion }}</p>
                            @endif
                            @if ($item->codigo_pais)
                                <p>Código País: {{ $item->codigo_pais }}</p>
                            @endif
                        </x-td>
                        
                        <x-td>
                            {{ $item->direccion }}
                        </x-td>
                        <x-td class="text-center">
                            @if ($item->logo_factura)
                                <img src="{{ Storage::disk('public')->url($item->logo_factura) }}" alt="Logo Factura"
                                    class="w-14 h-14 object-cover rounded-lg shadow m-auto">
                            @else
                                <p class="text-sm">Sin Logo</p>
                            @endif
                        </x-td>
                        <x-td class="text-center">
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
                        </x-td>
                        <x-td class="text-left">
                            @php
                                $agrupado = $item->informacionAdicional->groupBy('ubicacion');
                            @endphp

                            @foreach (['Cabecera', 'Centro', 'Pie'] as $seccion)
                                @if ($agrupado->has($seccion))
                                    <div class="mb-2">
                                        <div class="font-semibold">{{ $seccion }}</div>
                                        <ul class="list-disc list-inside ml-2">
                                            @foreach ($agrupado[$seccion] as $info)
                                                <li>{{ $info->clave }}: {{ $info->valor }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endforeach
                        </x-td>
                    </x-tr>
                @endforeach
            </x-slot>
        </x-table>
    @endif
</x-card>