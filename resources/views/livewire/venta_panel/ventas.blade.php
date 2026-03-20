<div class="space-y-4">
    <x-title>Gestión de Ventas</x-title>
    <x-card>
        <x-flex>
            <flux:select wire:model.live="filtroSucursal" label="Filtrar por Sucursal" class="w-64">
                <flux:select.option value="">-- Filtrar por Sucursal --</flux:select.option>
                @foreach ($sucursales as $sucursal)
                    <flux:select.option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input wire:model.live="filtroCliente" label="Nombre del cliente" />
            <flux:input type="date" wire:model.live="filtroDesde" label="Fecha Desde" />
            <flux:input type="date" wire:model.live="filtroHasta" label="Fecha Hasta" />
        </x-flex>

        <div x-data="{ openVentaId: null }" class="mt-3">
            <x-table>
                <x-slot name="thead">
                    <x-th class="text-left">Sucursal</x-th>
                    <x-th class="text-left">Cliente</x-th>
                    <x-th class="text-center">Fecha</x-th>
                    <x-th class="text-center">Comprobante</x-th>
                    <x-th class="text-center">Modo</x-th>
                    <x-th class="text-right">Total</x-th>
                    <x-th class="text-center">Documentos</x-th>
                    <x-th class="text-center">Estado SUNAT</x-th>
                    <x-th class="text-center">Acciones</x-th>
                </x-slot>

                <x-slot name="tbody">
                    @forelse($ventas as $venta)
                        {{-- Fila principal --}}
                        <tr>
                            {{-- Sucursal --}}
                            <x-td class="text-left text-sm">
                                {{ $venta->sucursal->nombre }}
                            </x-td>

                            {{-- Cliente + documento --}}
                            <x-td class="text-left">
                                <div class="text-sm font-medium truncate max-w-[130px]">{{ $venta->nombre_cliente }}
                                </div>
                                <div class="text-xs ">{{ $venta->documento_cliente }}</div>
                            </x-td>

                            {{-- Fecha --}}
                            <x-td class="text-center text-sm">
                                {{ $venta->fecha_emision }}
                            </x-td>

                            {{-- Comprobante: serie-número + tipo --}}
                            <x-td class="text-center">
                                <div class="text-sm font-medium">
                                    {{ $venta->serie_comprobante }}-{{ $venta->correlativo_comprobante }}
                                </div>
                                <div class="text-xs ">
                                    {{ $venta->comprobante?->nombre ?? $venta->tipo_comprobante_codigo }}
                                </div>
                            </x-td>

                            {{-- Modo --}}
                            <x-td class="text-center">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-medium rounded-full
                                    {{ $venta->modo_venta === 'produccion' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $venta->modo_venta === 'produccion' ? 'Prod' : 'Demo' }}
                                </span>
                            </x-td>

                            {{-- Total --}}
                            <x-td class="text-right font-medium text-sm tabular-nums">
                                S/ {{ number_format($venta->monto_importe_venta, 2) }}
                            </x-td>

                            {{-- Documentos: solo visibles si aceptada --}}
                            <x-td class="text-center">
                                <div class="flex justify-center gap-1">
                                    @if ($venta->sunat_estado === 'aceptada')
                                        @if ($venta->sunat_comprobante_pdf)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_comprobante_pdf) }}"
                                                target="_blank" download title="PDF Factura"
                                                class="text-blue-600 hover:underline">
                                                <img src="{{ asset('image/pdf.png') }}" width="32px" alt="PDF">

                                            </a>
                                        @endif
                                        @if ($venta->voucher_pdf)
                                            <a href="{{ Storage::disk('public')->url($venta->voucher_pdf) }}"
                                                target="_blank" download title="Voucher"
                                                class="text-blue-600 hover:underline">
                                                <img src="{{ asset('image/pdf.png') }}" width="32px" alt="PDF">

                                            </a>
                                        @endif
                                        @if ($venta->sunat_xml_firmado)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_xml_firmado) }}"
                                                target="_blank" download title="XML"
                                                class="text-blue-600 hover:underline">
                                                <img src="{{ asset('image/xml.png') }}" width="32px"
                                                    alt="Archivo XML">

                                            </a>
                                        @endif
                                        @if ($venta->sunat_cdr)
                                            <a href="{{ Storage::disk('public')->url($venta->sunat_cdr) }}"
                                                target="_blank" download title="CDR"
                                                class="text-blue-600 hover:underline">
                                                <img src="{{ asset('image/cdr.png') }}" width="32px"
                                                    alt="Archivo CDR">
                                            </a>
                                        @endif
                                    @elseif ($venta->tipo_comprobante_codigo == 'ticket')
                                        @if ($venta->voucher_pdf)
                                            <a href="{{ Storage::disk('public')->url($venta->voucher_pdf) }}"
                                                target="_blank" download title="Voucher"
                                                class="text-blue-600 hover:underline">
                                                <img src="{{ asset('image/pdf.png') }}" width="32px" alt="PDF">

                                            </a>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-300 italic">—</span>
                                    @endif
                                </div>
                            </x-td>

                            {{-- Estado SUNAT --}}
                            <x-td class="text-center">
                                @php
                                    $estadoConfig = match ($venta->sunat_estado) {
                                        'aceptada' => ['bg-green-100 text-green-800', 'Aceptada'],
                                        'rechazada' => ['bg-red-100 text-red-800', 'Rechazada'],
                                        'rechazada_requiere_nuevo_correlativo' => [
                                            'bg-red-100 text-red-800',
                                            'Nuevo N° requerido',
                                        ],
                                        'pendiente_sunat' => ['bg-yellow-100 text-yellow-800', 'Pendiente'],
                                        'error_sistema' => ['bg-orange-100 text-orange-800', 'Error sistema'],
                                        'error_fecha' => ['bg-orange-100 text-orange-800', 'Error fecha'],
                                        'descartada' => ['bg-gray-100 text-gray-700', 'Descartada'],
                                        default => ['bg-gray-100 text-gray-700', 'Sin enviar'],
                                    };
                                @endphp
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-medium rounded-full {{ $estadoConfig[0] }}">
                                    {{ $estadoConfig[1] }}
                                </span>

                                {{-- Mensaje CDR resumido si hay error --}}
                                @if ($venta->sunat_cdr_codigo && $venta->sunat_estado !== 'aceptada')
                                    <div class="text-xs mt-0.5">Cód: {{ $venta->sunat_cdr_codigo }}</div>
                                @endif
                            </x-td>

                            {{-- Acciones: botón detalle + dropdown opciones --}}
                            <x-td class="text-center">
                                <div class="flex justify-center items-center gap-1">

                                    {{-- Botón ver detalle (toggle inline) --}}
                                    <flux:button size="sm"
                                        @click="openVentaId === '{{ $venta->id }}' ? openVentaId = null : openVentaId = '{{ $venta->id }}'"
                                        title="Ver detalle">
                                        <svg width="10" height="10" viewBox="0 0 16 16" fill="none"
                                            stroke="currentColor" stroke-width="1.5">
                                            <path d="M2 4h12M2 8h12M2 12h7" />
                                        </svg>
                                    </flux:button>

                                    {{-- Dropdown opciones --}}
                                    <flux:dropdown>
                                        <flux:button size="sm" icon:trailing="chevron-down">Opciones</flux:button>
                                        <flux:menu>

                                            {{-- Duplicar --}}
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

                                            {{-- Revalidar --}}
                                            @if ($venta->estado !== 'anulado')
                                                <flux:menu.item icon="clipboard-document-check"
                                                    wire:click="revalidarVenta('{{ $venta->uuid }}')">
                                                    Revalidar
                                                </flux:menu.item>
                                            @endif

                                            {{-- Reenviar a SUNAT --}}
                                            @if (in_array($venta->sunat_estado, ['error_sistema', 'error_fecha', 'pendiente_sunat', null]) &&
                                                    in_array($venta->tipo_comprobante_codigo, ['01', '03']))
                                                <flux:menu.item icon="arrow-path"
                                                    wire:click="reenviarSunat('{{ $venta->uuid }}')">
                                                    Reenviar a SUNAT
                                                </flux:menu.item>
                                            @endif

                                            {{-- Regularizar --}}
                                            @if (in_array($venta->sunat_estado, ['rechazada', 'rechazada_requiere_nuevo_correlativo', null]))
                                                <flux:menu.item icon="document-arrow-up"
                                                    @click="$wire.dispatch('abrirRegularizacion', { uuid: '{{ $venta->uuid }}' })">
                                                    Regularizar factura
                                                </flux:menu.item>
                                            @endif

                                            {{-- Anular con nota de crédito (solo facturas) --}}
                                            @if ($venta->tipo_comprobante_codigo === '01' && $venta->sunat_estado === 'aceptada')
                                                <flux:menu.item icon="document-arrow-down"
                                                    @click="$wire.dispatch('generarNota', { modo: 'anulacion', uuid: '{{ $venta->uuid }}' })">
                                                    Anular (nota de crédito)
                                                </flux:menu.item>
                                            @endif

                                            {{-- Anular venta --}}
                                            <flux:menu.item icon="arrow-left-start-on-rectangle" variant="danger"
                                                wire:confirm="¿Anular venta?"
                                                wire:click="anularVenta('{{ $venta->uuid }}')">
                                                Anular Venta
                                            </flux:menu.item>

                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </x-td>
                        </tr>

                        {{-- Fila detalle expandible --}}
                        <x-tr x-show="openVentaId === '{{ $venta->id }}'" x-cloak
                            class="bg-zinc-50 dark:bg-zinc-950">
                            <x-td colspan="100%">

                                {{-- Info SUNAT si hay error --}}
                                @if ($venta->sunat_estado !== 'aceptada' && $venta->sunat_cdr_descripcion)
                                    <div
                                        class="mb-3 px-3 py-2 rounded text-xs border-l-2
                                        {{ in_array($venta->sunat_estado, [
                                            'rechazada',
                                            'rechazada_requiere_nuevo_correlativo',
                                            'error_sistema',
                                            'error_fecha',
                                        ])
                                            ? 'bg-red-50 border-red-400 text-red-700'
                                            : 'bg-yellow-50 border-yellow-400 text-yellow-700' }}">
                                        <span class="font-medium">SUNAT:</span> {{ $venta->sunat_cdr_descripcion }}
                                    </div>
                                @endif

                                {{-- Referencia a comprobante origen si es regularización --}}
                                @if ($venta->serie_origen && $venta->correlativo_origen)
                                    <div
                                        class="mb-3 px-3 py-2 rounded text-xs bg-blue-50 border-l-2 border-blue-400 text-blue-700">
                                        Regulariza al comprobante
                                        <span
                                            class="font-medium">{{ $venta->serie_origen }}-{{ $venta->correlativo_origen }}</span>
                                        @if ($venta->motivo_regularizacion)
                                            — {{ $venta->motivo_regularizacion }}
                                        @endif
                                    </div>
                                @endif
                                {{-- Resumen completo de la venta --}}
                                <x-card class="mb-3">
                                    <flux:heading size="sm">Resumen de venta</flux:heading>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3 text-sm">

                                        {{-- Columna 1: Comprobante --}}
                                        <div class="space-y-1">
                                            <p class="text-xs font-medium uppercase tracking-wide mb-2">
                                                Comprobante</p>
                                            <div class="flex justify-between">
                                                <span class="">ID Venta</span>
                                                <span class="font-medium">{{ $venta->id }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="">Serie - Número</span>
                                                <span
                                                    class="font-medium">{{ $venta->serie_comprobante }}-{{ $venta->correlativo_comprobante }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="">Tipo</span>
                                                <span
                                                    class="font-medium">{{ $venta->comprobante?->nombre ?? $venta->tipo_comprobante_codigo }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="">Fecha emisión</span>
                                                <span class="font-medium">{{ $venta->fecha_emision }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="">Modo</span>
                                                <span class="font-medium">{{ ucfirst($venta->modo_venta) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="">Estado pago</span>
                                                <span class="font-medium">{{ ucfirst($venta->estado) }}</span>
                                            </div>
                                            @if ($venta->serie_origen && $venta->correlativo_origen)
                                                <div class="flex justify-between">
                                                    <span class="">Regulariza a</span>
                                                    <span
                                                        class="font-medium text-blue-600">{{ $venta->serie_origen }}-{{ $venta->correlativo_origen }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Columna 2: Cliente --}}
                                        <div class="space-y-1">
                                            <p class="text-xs font-medium uppercase tracking-wide mb-2">
                                                Cliente</p>
                                            <div class="flex justify-between">
                                                <span class="">ID Cliente</span>
                                                <span class="font-medium text-right max-w-[180px] truncate"
                                                    title="{{ $venta->cliente_id }}">
                                                    {{ $venta->cliente_id }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="">Nombre</span>
                                                <span class="font-medium text-right max-w-[180px] truncate"
                                                    title="{{ $venta->nombre_cliente }}">
                                                    {{ $venta->nombre_cliente }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="">Tipo doc.</span>
                                                <span class="font-medium">
                                                    {{ match ((string) $venta->tipo_documento_cliente) {
                                                        '1' => 'DNI',
                                                        '4' => 'Carnet Ext.',
                                                        '6' => 'RUC',
                                                        '7' => 'Pasaporte',
                                                        default => $venta->tipo_documento_cliente ?? '—',
                                                    } }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="">Documento</span>
                                                <span class="font-medium">{{ $venta->documento_cliente }}</span>
                                            </div>
                                            @if ($venta->cliente_direccion)
                                                <div class="flex justify-between gap-2">
                                                    <span class=" shrink-0">Dirección</span>
                                                    <span
                                                        class="font-medium text-right text-xs">{{ $venta->cliente_direccion }}</span>
                                                </div>
                                            @endif
                                            @if ($venta->cliente_distrito || $venta->cliente_provincia)
                                                <div class="flex justify-between">
                                                    <span class="">Ubicación</span>
                                                    <span class="font-medium text-right text-xs">
                                                        {{ implode(', ', array_filter([$venta->cliente_distrito, $venta->cliente_provincia, $venta->cliente_departamento])) }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if ($venta->cliente_email)
                                                <div class="flex justify-between">
                                                    <span class="">Email</span>
                                                    <span
                                                        class="font-medium text-xs">{{ $venta->cliente_email }}</span>
                                                </div>
                                            @endif
                                            @if ($venta->cliente_telefono)
                                                <div class="flex justify-between">
                                                    <span class="">Teléfono</span>
                                                    <span class="font-medium">{{ $venta->cliente_telefono }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Columna 3: Montos --}}
                                        <div class="space-y-1">
                                            <p class="text-xs font-medium uppercase tracking-wide mb-2">
                                                Montos</p>
                                            @if ($venta->monto_operaciones_gravadas > 0)
                                                <div class="flex justify-between">
                                                    <span class="">Op. gravadas</span>
                                                    <span class="font-medium tabular-nums">S/
                                                        {{ number_format($venta->monto_operaciones_gravadas, 2) }}</span>
                                                </div>
                                            @endif
                                            @if ($venta->monto_operaciones_exoneradas > 0)
                                                <div class="flex justify-between">
                                                    <span class="">Op. exoneradas</span>
                                                    <span class="font-medium tabular-nums">S/
                                                        {{ number_format($venta->monto_operaciones_exoneradas, 2) }}</span>
                                                </div>
                                            @endif
                                            @if ($venta->monto_operaciones_inafectas > 0)
                                                <div class="flex justify-between">
                                                    <span class="">Op. inafectas</span>
                                                    <span class="font-medium tabular-nums">S/
                                                        {{ number_format($venta->monto_operaciones_inafectas, 2) }}</span>
                                                </div>
                                            @endif
                                            @if ($venta->monto_operaciones_gratuitas > 0)
                                                <div class="flex justify-between">
                                                    <span class="">Op. gratuitas</span>
                                                    <span class="font-medium tabular-nums">S/
                                                        {{ number_format($venta->monto_operaciones_gratuitas, 2) }}</span>
                                                </div>
                                            @endif
                                            <div class="flex justify-between">
                                                <span class="">IGV (18%)</span>
                                                <span class="font-medium tabular-nums">S/
                                                    {{ number_format($venta->monto_igv, 2) }}</span>
                                            </div>
                                            @if ($venta->icbper > 0)
                                                <div class="flex justify-between">
                                                    <span class="">ICBPER</span>
                                                    <span class="font-medium tabular-nums">S/
                                                        {{ number_format($venta->icbper, 2) }}</span>
                                                </div>
                                            @endif
                                            @if ($venta->redondeo != 0)
                                                <div class="flex justify-between">
                                                    <span class="">Redondeo</span>
                                                    <span class="font-medium tabular-nums">S/
                                                        {{ number_format($venta->redondeo, 2) }}</span>
                                                </div>
                                            @endif
                                            <div class="flex justify-between border-t border-border pt-1 mt-1">
                                                <span class=" font-medium">Total a pagar</span>
                                                <span class="font-semibold tabular-nums text-accent">S/
                                                    {{ number_format($venta->monto_importe_venta, 2) }}</span>
                                            </div>

                                            {{-- Estado SUNAT --}}
                                            <div class="flex justify-between pt-2 mt-1 border-t border-gray-200">
                                                <span class="">Estado SUNAT</span>
                                                @php
                                                    $estadoConfig = match ($venta->sunat_estado) {
                                                        'aceptada' => ['bg-green-100 text-green-800', 'Aceptada'],
                                                        'rechazada' => ['bg-red-100 text-red-800', 'Rechazada'],
                                                        'rechazada_requiere_nuevo_correlativo' => [
                                                            'bg-red-100 text-red-800',
                                                            'Nuevo N° requerido',
                                                        ],
                                                        'pendiente_sunat' => [
                                                            'bg-yellow-100 text-yellow-800',
                                                            'Pendiente',
                                                        ],
                                                        'error_sistema' => [
                                                            'bg-orange-100 text-orange-800',
                                                            'Error sistema',
                                                        ],
                                                        'error_fecha' => [
                                                            'bg-orange-100 text-orange-800',
                                                            'Error fecha',
                                                        ],
                                                        'descartada' => ['bg-gray-100 text-gray-800', 'Descartada'],
                                                        default => ['bg-gray-100  text-gray-800', 'Sin enviar'],
                                                    };
                                                @endphp
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-medium rounded-full {{ $estadoConfig[0] }}">
                                                    {{ $estadoConfig[1] }}
                                                </span>
                                            </div>

                                            @if ($venta->sunat_cdr_descripcion && $venta->sunat_estado !== 'aceptada')
                                                <div
                                                    class="text-xs mt-1 px-2 py-1.5 rounded border-l-2
                    {{ str_contains($venta->sunat_estado ?? '', 'error') || str_contains($venta->sunat_estado ?? '', 'rechazada')
                        ? 'bg-red-50 border-red-400 text-red-700'
                        : 'bg-yellow-50 border-yellow-400 text-yellow-700' }}">
                                                    {{ $venta->sunat_cdr_descripcion }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </x-card>
                                {{-- Productos --}}
                                <x-card class="mb-3">
                                    <flux:heading size="sm">Detalle de productos</flux:heading>
                                    <x-table class="w-full text-sm mt-2">

                                        <x-slot:thead>
                                            <x-tr class="text-left">
                                                <x-th class="">Descripción</x-th>
                                                <x-th class=" text-center">Cant.</x-th>
                                                <x-th class=" text-right">P. Unitario</x-th>
                                                <x-th class=" text-right">IGV</x-th>
                                                <x-th class=" text-right">Total</x-th>
                                            </x-tr>
                                        </x-slot:thead>

                                        <x-slot:tbody>
                                            @foreach ($venta->detalles as $detalle)
                                                <x-tr>
                                                    <x-td class="">{{ $detalle->descripcion }}</x-td>
                                                    <x-td class=" text-center">{{ $detalle->cantidad }}</x-td>
                                                    <x-td class=" text-right">
                                                        S/ {{ number_format($detalle->monto_precio_unitario, 2) }}
                                                    </x-td>
                                                    <x-td class=" text-right">
                                                        S/ {{ number_format($detalle->igv, 2) }}
                                                    </x-td>
                                                    <x-td class=" text-right">
                                                        S/
                                                        {{ number_format($detalle->monto_valor_venta + $detalle->igv, 2) }}
                                                    </x-td>
                                                </x-tr>
                                            @endforeach
                                        </x-slot:tbody>

                                    </x-table>
                                </x-card>

                                {{-- Notas de crédito vinculadas --}}
                                @if ($venta->notas && $venta->notas->count() > 0)
                                    <x-card class="">
                                        <flux:heading size="sm">Notas emitidas</flux:heading>
                                        <x-table class="w-full text-sm mt-2">
                                            <x-slot name="thead">
                                                <x-tr>
                                                    <x-th class="px-2 py-1">Nota</x-th>
                                                    <x-th class="px-2 py-1">Fecha</x-th>
                                                    <x-th class="px-2 py-1">Motivo</x-th>
                                                    <x-th class="px-2 py-1 text-right">Total</x-th>
                                                    <x-th class="px-2 py-1 text-center">Docs</x-th>
                                                </x-tr>
                                            </x-slot>
                                            <x-slot name="tbody">
                                                @foreach ($venta->notas as $nota)
                                                    <tr class="border-t">
                                                        <td class="px-2 py-1 font-medium">
                                                            NC{{ $nota->serie_comprobante }}-{{ $nota->correlativo_comprobante }}
                                                        </td>
                                                        <td class="px-2 py-1">{{ $nota->fecha_emision }}</td>
                                                        <td class="px-2 py-1">{{ $nota->des_motivo }}</td>
                                                        <td class="px-2 py-1 text-right">S/
                                                            {{ number_format($nota->mto_imp_venta ?? 0, 2) }}</td>
                                                        <td class="px-2 py-1">
                                                            <div class="flex gap-1 justify-center">
                                                                @if ($nota->sunat_comprobante_pdf)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_comprobante_pdf) }}"
                                                                        target="_blank" download
                                                                        class="text-blue-600 hover:underline">
                                                                        <img src="{{ asset('image/pdf.png') }}"
                                                                            width="32px" alt="PDF">
                                                                    </a>
                                                                @endif
                                                                @if ($nota->sunat_xml_firmado)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_xml_firmado) }}"
                                                                        target="_blank" download
                                                                        class="text-blue-600 hover:underline">
                                                                        <img src="{{ asset('image/xml.png') }}"
                                                                            width="32px" alt="Archivo XML">

                                                                    </a>
                                                                @endif
                                                                @if ($nota->sunat_cdr)
                                                                    <a href="{{ Storage::disk('public')->url($nota->sunat_cdr) }}"
                                                                        target="_blank" download
                                                                        class="text-blue-600 hover:underline">
                                                                        <img src="{{ asset('image/cdr.png') }}"
                                                                            width="32px" alt="Archivo CDR">
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </x-slot>
                                        </x-table>
                                    </x-card>
                                @endif

                            </x-td>
                        </x-tr>

                    @empty
                        <x-table.empty colspan="100%" />
                    @endforelse
                </x-slot>
            </x-table>
        </div>

        <div class="mt-4">
            {{ $ventas->links() }}
        </div>
    </x-card>

    <livewire:sunat.operaciones.emitir-nota-credito />
    <livewire:sunat.operaciones.regularizar-factura />
    <x-loading wire:loading />
</div>
