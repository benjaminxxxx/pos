<div>
    <x-loading wire:loading />
    <x-card>
        @if ($negocioSeleccionado)
            <x-flex class="justify-between mb-4">
                <div>
                    <x-h2>Gestión de Ventas</x-h2>
                    <flux:heading>Negocio: {{ $negocioSeleccionado->nombre_legal }}</flux:heading>
                </div>
                <div class="flex space-x-2">
                    <flux:button wire:click="cambiarNegocio" variant="filled">
                        Cambiar Negocio
                    </flux:button>
                </div>
            </x-flex>

            <x-flex>
                <flux:select wire:model.live="filtroSucursal" label="Filtrar por Sucursal" class="w-64">
                    <flux:select.option value="">-- Filtrar por Sucursal --</flux:select.option>
                    @foreach ($sucursales as $sucursal)
                        <flux:select.option value="{{ $sucursal->id }}">
                            {{ $sucursal->nombre }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model.live="filtroCliente" label="Nombre del cliente"/>
                <flux:input type="date" wire:model.live="filtroDesde" label="Fecha Desde"/>
                <flux:input type="date" wire:model.live="filtroHasta" label="Fecha Hasta"/>
            </x-flex>

            <div x-data="{ openVentaId: null }" class="mt-3">
                <x-table>
                    <x-slot name="thead">
                        <x-table.th class="text-left">Sucursal</x-table.th>
                        <x-table.th class="text-left">Cliente</x-table.th>
                        <x-table.th class="text-center">Fecha</x-table.th>
                        <x-table.th class="text-center">Comprobante</x-table.th>
                        <x-table.th class="text-right">Total</x-table.th>
                        <x-table.th class="text-center">Documentos</x-table.th>
                        <x-table.th class="text-center">Estado</x-table.th>
                        <x-table.th class="text-center">Acciones</x-table.th>
                    </x-slot>

                    <x-slot name="tbody">
                        @forelse($ventas as $venta)
                            <tr>
                                <x-table.td class="text-left">{{ $venta->sucursal->nombre }}</x-table.td>
                                <x-table.td class="text-left">{{ $venta->nombre_cliente }}</x-table.td>
                                <x-table.td class="text-center">{{ $venta->fecha_emision }}</x-table.td>
                                <x-table.td class="text-center">{{ $venta->comprobante?->nombre }}</x-table.td>
                                <x-table.td class="text-right">S/
                                    {{ number_format($venta->monto_importe_venta, 2) }}</x-table.td>
                                <x-table.td class="text-center">
                                    <x-flex class="justify-center">
                                        @if ($venta->sunat_comprobante_pdf)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_comprobante_pdf) }}"
                                                target="_blank" class="text-blue-600 hover:underline" download>
                                                <img src="{{ asset('image/pdf.png') }}" width="32px" alt="PDF">
                                            </a>
                                        @endif
                                        @if ($venta->voucher_pdf)
                                            <a href="{{ Storage::disk('public')->url($venta->voucher_pdf) }}" target="_blank"
                                                class="text-blue-600 hover:underline" download>
                                                <img src="{{ asset('image/pdf.png') }}" width="32px" alt="PDF">
                                            </a>
                                        @endif
                                        @if ($venta->sunat_xml_firmado)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_xml_firmado) }}" target="_blank"
                                                class="text-blue-600 hover:underline" download>
                                                <img src="{{ asset('image/xml.png') }}" width="32px" alt="Archivo XML">
                                            </a>
                                        @endif
                                        @if ($venta->sunat_cdr)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_cdr) }}" target="_blank"
                                                class="text-blue-600 hover:underline" download>

                                                <img src="{{ asset('image/cdr.png') }}" width="32px" alt="Archivo CDR">
                                            </a>
                                        @endif


                                    </x-flex>
                                </x-table.td>
                                <x-table.td class="text-center">
                                    <div>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $venta->estado === 'pagado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($venta->estado) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $venta->modo_venta !== 'desarrollo' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($venta->modo_venta) }}
                                        </span>
                                    </div>
                                </x-table.td>
                                <x-table.td class="text-center">
                                    <div class="flex justify-center gap-3">
                                        <flux:dropdown>
                                            <flux:button icon:trailing="chevron-down">Opciones</flux:button>
                                            <flux:menu>
                                                <form id="formDuplicar-{{ $venta->uuid }}"
                                                    action="{{ route('venta_panel.ventas.duplicar') }}" method="POST"
                                                    class="hidden">
                                                    @csrf
                                                    <input type="hidden" name="uuid" value="{{ $venta->uuid }}">
                                                </form>

                                                <flux:menu.item icon="document-duplicate"
                                                    onclick="document.getElementById('formDuplicar-{{ $venta->uuid }}').submit()">
                                                    Duplicar
                                                </flux:menu.item>

                                                @if($venta->estado != 'anulado')
                                                    <flux:menu.item icon="clipboard-document-check"
                                                        wire:click="revalidarVenta('{{ $venta->uuid }}')">
                                                        Revalidar
                                                    </flux:menu.item>
                                                @endif
                                                <!-- Ver Detalles -->
                                                <flux:menu.item icon="eye"
                                                    @click="openVentaId === '{{ $venta->id }}' ? openVentaId = null : openVentaId = '{{ $venta->id }}'">
                                                    Ver Detalles
                                                </flux:menu.item>

                                                <!-- Anular Factura -->
                                                @if ($venta->tipo_comprobante_codigo === '01')
                                                    <flux:menu.item icon="document-arrow-down"
                                                        @click="$wire.dispatch('generarNota',{modo:'anulacion',uuid:'{{ $venta->uuid }}'})">
                                                        Anular factura (nota de crédito)
                                                    </flux:menu.item>
                                                @endif

                                                <!-- Eliminar -->
                                                <flux:menu.item icon="arrow-left-start-on-rectangle" variant="danger"
                                                    wire:confirm="Anular venta?" wire:click="anularVenta('{{ $venta->uuid }}')">
                                                    Anular Venta
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>

                                    </div>
                                </x-table.td>
                            </tr>

                            <tr x-show="openVentaId === '{{ $venta->id }}'" x-cloak class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="100%" class="p-2">
                                    <x-card class="mb-4 dark:bg-gray-900">
                                        <flux:heading>
                                            Detalle de venta
                                        </flux:heading>
                                        <table class="w-full text-sm">
                                            <thead class="text-left bg-gray-100 dark:bg-gray-700">
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
                                    </x-card>


                                    @if ($venta->notas && $venta->notas->count() > 0)
                                        <x-card>
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
                                                                {{ number_format($nota->mto_imp_venta ?? 0, 2) }}
                                                            </td>
                                                            <td class="px-2 py-1 flex gap-2">
                                                                @if ($nota->sunat_comprobante_pdf)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_comprobante_pdf) }}"
                                                                        target="_blank" download>
                                                                        <img src="{{ asset('image/pdf.png') }}" width="24px" alt="PDF">
                                                                    </a>
                                                                @endif
                                                                @if ($nota->sunat_xml_firmado)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_xml_firmado) }}"
                                                                        target="_blank" download>
                                                                        <img src="{{ asset('image/xml.png') }}" width="24px" alt="XML">
                                                                    </a>
                                                                @endif
                                                                @if ($nota->sunat_cdr)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_cdr) }}"
                                                                        target="_blank" download>
                                                                        <img src="{{ asset('image/cdr.png') }}" width="24px" alt="CDR">
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>

                                        </x-card>
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