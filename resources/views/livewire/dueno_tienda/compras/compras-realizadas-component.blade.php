<div>
    {{-- ── Notificaciones ─────────────────────────────────────────────────── --}}
    <x-on:notificacion.window="
        if ($event.detail.tipo === 'success') {
            $wire.$js.alert('success', $event.detail.mensaje)
        } else {
            $wire.$js.alert('error', $event.detail.mensaje)
        }
    " />

    {{-- ── Cabecera ────────────────────────────────────────────────────────── --}}
    <x-flex class="justify-between items-center">
        <x-title>Compras</x-title>
    </x-flex>

    {{-- ── Filtros ─────────────────────────────────────────────────────────── --}}
    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">

        <flux:input
            wire:model.live.debounce.400ms="busqueda"
            placeholder="N° comprobante, proveedor o RUC…"
            icon="magnifying-glass"
            class="col-span-2 sm:col-span-3 lg:col-span-2"
        />

        <flux:select wire:model.live="mes" placeholder="Mes">
            <flux:select.option value="">Todos los meses</flux:select.option>
            @foreach([
                '01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril',
                '05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto',
                '09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'
            ] as $num => $nombre)
                <flux:select.option value="{{ $num }}">{{ $nombre }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="anio" placeholder="Año">
            <flux:select.option value="">Todos los años</flux:select.option>
            @foreach($anios as $a)
                <flux:select.option value="{{ $a }}">{{ $a }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="estadoPago" placeholder="Estado pago">
            <flux:select.option value="">Todos</flux:select.option>
            <flux:select.option value="PENDIENTE">Pendiente</flux:select.option>
            <flux:select.option value="PAGADO">Pagado</flux:select.option>
            <flux:select.option value="ANULADO">Anulado</flux:select.option>
        </flux:select>

    </div>

    {{-- ── Tabla ───────────────────────────────────────────────────────────── --}}
    <x-card class="mt-4">
        <x-table>
            <x-slot name="thead">
                <x-tr>
                    <x-th>N°</x-th>
                    <x-th>Fecha</x-th>
                    <x-th>Comprobante</x-th>
                    <x-th>Proveedor</x-th>
                    <x-th>Sucursal</x-th>
                    <x-th class="text-right">Subtotal</x-th>
                    <x-th class="text-right">IGV</x-th>
                    <x-th class="text-right">Total</x-th>
                    <x-th>Pago</x-th>
                    <x-th>Estado</x-th>
                    <x-th></x-th>
                </x-tr>
            </x-slot>

            <x-slot name="tbody">
                @forelse($compras as $compra)
                    @php
                        $anulada = $compra->estado === false || $compra->estado_pago === 'ANULADO';
                    @endphp
                    <x-tr class="{{ $anulada ? 'opacity-50' : '' }}">
                        <x-td>{{ $compras->firstItem() + $loop->index }}</x-td>

                        <x-td>{{ $compra->fecha_comprobante->format('d/m/Y') }}</x-td>

                        <x-td>
                            <div class="text-xs text-zinc-500">{{ $compra->tipo_comprobante }}</div>
                            <div class="font-mono text-sm">{{ $compra->numero_comprobante ?? '—' }}</div>
                        </x-td>

                        <x-td>
                            <div class="font-medium">
                                {{ $compra->proveedor_razon_social ?? $compra->proveedor?->razon_social ?? '—' }}
                            </div>
                            <div class="text-xs text-zinc-500">
                                {{ $compra->proveedor_documento_numero ?? '—' }}
                            </div>
                        </x-td>

                        <x-td>{{ $compra->sucursal?->nombre ?? '—' }}</x-td>

                        <x-td class="text-right font-mono">
                            S/ {{ number_format($compra->subtotal, 2) }}
                        </x-td>

                        <x-td class="text-right font-mono">
                            S/ {{ number_format($compra->igv, 2) }}
                        </x-td>

                        <x-td class="text-right font-mono font-semibold">
                            S/ {{ number_format($compra->total, 2) }}
                        </x-td>

                        <x-td>
                            <span class="text-xs">{{ $compra->forma_pago }}</span>
                        </x-td>

                        <x-td>
                            @if($anulada)
                                <flux:badge color="red" size="sm">Anulado</flux:badge>
                            @elseif($compra->estado_pago === 'PAGADO')
                                <flux:badge color="green" size="sm">Pagado</flux:badge>
                            @else
                                <flux:badge color="yellow" size="sm">Pendiente</flux:badge>
                            @endif
                        </x-td>

                        <x-td>
                            @if(!$anulada)
                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    wire:click="confirmarAnulacion({{ $compra->id }})"
                                    title="Anular compra"
                                >
                                    Anular
                                </flux:button>
                            @else
                                <span class="text-xs text-zinc-400 italic">Anulada</span>
                            @endif
                        </x-td>
                    </x-tr>
                @empty
                    <x-tr>
                        <x-td colspan="11" class="text-center py-10 text-zinc-400">
                            No se encontraron compras con los filtros aplicados.
                        </x-td>
                    </x-tr>
                @endforelse
            </x-slot>
        </x-table>

        <div class="mt-4">
            {{ $compras->links() }}
        </div>
    </x-card>

    {{-- ── Modal confirmación anulación ───────────────────────────────────── --}}
    <flux:modal wire:model="mostrarModalAnular" class="max-w-md">
        <div class="p-6 space-y-4">
            <flux:heading size="lg">¿Anular esta compra?</flux:heading>

            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Esta acción revertirá el stock ingresado por la compra.
                <strong>Solo es posible si los productos aún no han sido vendidos.</strong>
                Una vez anulada, no se puede deshacer.
            </p>

            <flux:field>
                <flux:label>Motivo de anulación <span class="text-red-500">*</span></flux:label>
                <flux:textarea
                    wire:model="motivoAnulacion"
                    placeholder="Ej: RUC incorrecto del proveedor, duplicado, etc."
                    rows="3"
                />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" wire:click="cerrarModalAnular">
                    Cancelar
                </flux:button>
                <flux:button
                    variant="danger"
                    wire:click="anularCompra"
                    wire:loading.attr="disabled"
                    wire:target="anularCompra"
                >
                    <span wire:loading.remove wire:target="anularCompra">Confirmar anulación</span>
                    <span wire:loading wire:target="anularCompra">Anulando…</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>