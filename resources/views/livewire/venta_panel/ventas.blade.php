<div>
    <x-loading wire:loading />
    <x-card>
        @if ($negocioSeleccionado)
            <x-flex class="justify-between mb-4">
                <div>
                    <x-h1>Gestión de Ventas</x-h1>
                    <flux:heading>Negocio: {{ $negocioSeleccionado->nombre_legal }}</flux:heading>
                </div>
                <div class="flex space-x-2">
                    <flux:button wire:click="cambiarNegocio" variant="filled">
                        Cambiar Negocio
                    </flux:button>
                </div>
            </x-flex>

            <div x-data="{ openVentaId: null }">
                <x-table>
                    <x-slot name="thead">
                        <x-table.th>Cliente</x-table.th>
                        <x-table.th>Fecha</x-table.th>
                        <x-table.th>Comprobante</x-table.th>
                        <x-table.th>Monto</x-table.th>
                        <x-table.th>IGV</x-table.th>
                        <x-table.th>Total</x-table.th>
                        <x-table.th>Documentos</x-table.th>
                        <x-table.th>Estado</x-table.th>
                        <x-table.th class="text-center">Acciones</x-table.th>
                    </x-slot>

                    <x-slot name="tbody">
                        @forelse($ventas as $venta)
                            <tr>
                                <x-table.td>{{ $venta->nombre_cliente }}</x-table.td>
                                <x-table.td class="text-center">{{ $venta->fecha_emision }}</x-table.td>
                                <x-table.td class="text-center">{{ $venta->comprobante?->nombre }}</x-table.td>
                                <x-table.td class="text-right">S/
                                    {{ number_format($venta->valor_venta, 2) }}</x-table.td>
                                <x-table.td class="text-right">S/
                                    {{ number_format($venta->total_impuestos, 2) }}</x-table.td>
                                <x-table.td class="text-right">S/
                                    {{ number_format($venta->monto_importe_venta, 2) }}</x-table.td>
                                <x-table.td class="text-right">
                                    <x-flex>
                                        @if ($venta->sunat_comprobante_pdf)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_comprobante_pdf) }}"
                                                target="_blank" class="text-blue-600 hover:underline" download>
                                                <img src="{{ asset('image/pdf.png') }}" width="32px" alt="PDF">
                                            </a>
                                        @endif
                                        @if ($venta->voucher_pdf)
                                            <a href="{{ Storage::disk('public')->url($venta->voucher_pdf) }}"
                                                target="_blank" class="text-blue-600 hover:underline" download>
                                                <img src="{{ asset('image/pdf.png') }}" width="32px" alt="PDF">
                                            </a>
                                        @endif
                                        @if ($venta->sunat_xml_firmado)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_xml_firmado) }}"
                                                target="_blank" class="text-blue-600 hover:underline" download>
                                                <img src="{{ asset('image/xml.png') }}" width="32px"
                                                    alt="Archivo XML">
                                            </a>
                                        @endif
                                        @if ($venta->sunat_cdr)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_cdr) }}"
                                                target="_blank" class="text-blue-600 hover:underline" download>

                                                <img src="{{ asset('image/cdr.png') }}" width="32px"
                                                    alt="Archivo CDR">
                                            </a>
                                        @endif


                                    </x-flex>
                                </x-table.td>
                                <x-table.td class="text-center">
                                    <div>
                                        Estado
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $venta->estado === 'pagado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($venta->estado) }}
                                        </span>
                                    </div>
                                    <div>
                                        Modo
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $venta->modo_venta !== 'desarrollo' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($venta->modo_venta) }}
                                        </span>
                                    </div>
                                </x-table.td>
                                <x-table.td class="text-center">
                                    <div class="flex justify-center gap-3">
                                        <flux:button size="sm" icon="eye"
                                            @click="openVentaId === '{{ $venta->id }}' ? openVentaId = null : openVentaId = '{{ $venta->id }}'">
                                            Ver Detalles
                                        </flux:button>
                                        <flux:dropdown>
                                            <flux:button icon:trailing="chevron-down">Más opciones</flux:button>
                                            <flux:menu>
                                                <flux:menu.item icon="document-arrow-down"
                                                    @click="$wire.dispatch('generarNota',{modo:'anulacion',uuid:'{{ $venta->uuid }}'})">
                                                    Anular factura (nota de crédito)
                                                </flux:menu.item>
                                                <flux:menu.item icon="trash" variant="danger"
                                                    wire:confirm="¿Eliminar venta?"
                                                    wire:click="eliminarVenta('{{ $venta->uuid }}')">Eliminar
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                    </div>
                                </x-table.td>
                            </tr>

                            <tr x-show="openVentaId === '{{ $venta->id }}'" x-cloak class="bg-gray-50">
                                <td colspan="100%" class="p-2">
                                    <flux:card class="mb-4">
                                        <flux:heading>
                                            Detalle de venta
                                        </flux:heading>
                                        <table class="w-full text-sm">
                                            <thead class="text-left bg-gray-100">
                                                <tr>
                                                    <th class="px-2 py-1">Descripción</th>
                                                    <th class="px-2 py-1">Cantidad</th>
                                                    <th class="px-2 py-1">P. Unitario</th>
                                                    <th class="px-2 py-1">IGV</th>
                                                    <th class="px-2 py-1">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($venta->detalles as $detalle)
                                                    <tr class="border-t">
                                                        <td class="px-2 py-1">{{ $detalle->descripcion }}</td>
                                                        <td class="px-2 py-1">{{ $detalle->cantidad }}</td>
                                                        <td class="px-2 py-1">S/
                                                            {{ number_format($detalle->monto_precio_unitario, 2) }}
                                                        </td>
                                                        <td class="px-2 py-1">S/ {{ number_format($detalle->igv, 2) }}
                                                        </td>
                                                        <td class="px-2 py-1">S/
                                                            {{ number_format($detalle->monto_valor_venta + $detalle->igv, 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </flux:card>


                                    @if ($venta->notas && $venta->notas->count() > 0)
                                        <flux:card>
                                            <flux:heading>
                                                Se emitieron las siguientes notas
                                            </flux:heading>
                                            <table class="w-full text-sm">
                                                <thead class="text-left bg-gray-100">
                                                    <tr>
                                                        <th class="px-2 py-1">Nota</th>
                                                        <th class="px-2 py-1">Fecha</th>
                                                        <th class="px-2 py-1">Motivo</th>
                                                        <th class="px-2 py-1">Total</th>
                                                        <th class="px-2 py-1">Documentos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($venta->notas as $nota)
                                                        <tr class="border-t">
                                                            <td class="px-2 py-1 font-medium">
                                                                NC{{ $nota->serie_comprobante }}-{{ $nota->correlativo_comprobante }}
                                                            </td>
                                                            <td class="px-2 py-1">{{ $nota->fecha_emision }}</td>
                                                            <td class="px-2 py-1">{{ $nota->des_motivo }}</td>
                                                            <td class="px-2 py-1">S/
                                                                {{ number_format($nota->mto_imp_venta ?? 0, 2) }}</td>
                                                            <td class="px-2 py-1 flex gap-2">
                                                                @if ($nota->sunat_comprobante_pdf)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_comprobante_pdf) }}"
                                                                        target="_blank" download>
                                                                        <img src="{{ asset('image/pdf.png') }}"
                                                                            width="24px" alt="PDF">
                                                                    </a>
                                                                @endif
                                                                @if ($nota->sunat_xml_firmado)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_xml_firmado) }}"
                                                                        target="_blank" download>
                                                                        <img src="{{ asset('image/xml.png') }}"
                                                                            width="24px" alt="XML">
                                                                    </a>
                                                                @endif
                                                                @if ($nota->sunat_cdr)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_cdr) }}"
                                                                        target="_blank" download>
                                                                        <img src="{{ asset('image/cdr.png') }}"
                                                                            width="24px" alt="CDR">
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>

                                        </flux:card>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-table.empty colspan="100%" />
                        @endforelse
                    </x-slot>
                </x-table>
            </div>


            <div class="mt-4">
                {{ $ventas->links() }}
            </div>
        @endif
    </x-card>

    <!-- Modal para seleccionar negocio -->
    <x-seleccionar-negocio-modal :mostrar="$mostrarModalSeleccionNegocio" :negocios="$negocios" />
    <livewire:sunat.operaciones.emitir-nota-credito />
</div>
