<div>
    
    <x-flex class="justify-between">
        <x-title>Entrada de productos</x-title>
        <div>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">Opciones</flux:button>
                <flux:menu>
                    <flux:menu.item @click="$wire.dispatch('registrarEntradaProducto')">
                        Registrar Entrada
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </x-flex>
    <x-card class="mt-4">
        <x-table>
            <x-slot name="thead">
                <x-tr>
                    <x-th>N°</x-th>
                    <x-th>Producto</x-th>
                    <x-th>Marca</x-th>
                    <x-th>Sucursal</x-th>
                    <x-th>F. Ingreso</x-th>
                    <x-th>Motivo</x-th>
                    <x-th>Cantidad</x-th>
                    <x-th>Stock disp.</x-th>
                    <x-th>Valor (S/)</x-th>
                    <x-th>Referencia</x-th>
                    <x-th>Acciones</x-th>
                </x-tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($entradas as $entrada)
                    <x-tr>
                        <x-td>{{ $loop->iteration }}</x-td>
                        <x-td>{{ $entrada->producto->descripcion ?? '—' }}</x-td>
                        <x-td>{{ $entrada->producto->marca->nombre ?? '—' }}</x-td>
                        <x-td>{{ $entrada->sucursal->nombre ?? 'Sucursal ' . ($entrada->sucursal_id ?? '—') }}</x-td>
                        <x-td>{{ optional(\Carbon\Carbon::parse($entrada->fecha_ingreso))->format('d/m/Y') }}</x-td>
                        <x-td>{{ $entrada->tipo_entrada }}</x-td>
                        <x-td class="text-right">{{ number_format($entrada->cantidad, 3) }}</x-td>
                        <x-td class="text-right">{{ number_format($entrada->stock_disponible ?? 0, 3) }}</x-td>
                        <x-td class="text-right">{{ number_format($entrada->costo_unitario ?? 0, 4) }}</x-td>
                        <x-td>
                            @if($entrada->referencia_tipo || $entrada->referencia_id)
                                {{ class_basename($entrada->referencia_tipo ?? '') }}
                                @if($entrada->referencia_id) - #{{ $entrada->referencia_id }} @endif
                            @else
                                —
                            @endif
                        </x-td>
                        <x-td>

                        </x-td>
                    </x-tr>
                @endforeach
            </x-slot>
        </x-table>

        <div class="mt-4">
            {{ $entradas->links() }}
        </div>
    </x-card>
</div>