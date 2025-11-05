<div>
    
    <x-flex class="justify-between">
        <x-title>Salida de productos</x-title>
        <div>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">Opciones</flux:button>
                <flux:menu>
                    <flux:menu.item @click="$wire.dispatch('registrarSalidaProducto')">
                        Registrar Salida
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
                    <x-th>Fecha</x-th>
                    <x-th>Producto</x-th>
                    <x-th>Sucursal</x-th>
                    <x-th>Cantidad</x-th>
                    <x-th>Costo Unitario</x-th>
                    <x-th>Tipo</x-th>
                    <x-th>Estado</x-th>
                </x-tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($salidas as $salida)
                    <x-tr>
                        <x-td>{{ $loop->iteration }}</x-td>
                        <x-td>{{ $salida->fecha_salida->format('d/m/Y')  }}</x-td>
                        <x-td>{{ $salida->producto->descripcion ?? '—' }}</x-td>
                        <x-td>{{ $salida->sucursal->nombre ?? 'Sucursal ' }}</x-td>
                        <x-td>
                            {{ number_format($salida->cantidad, 2) }}
                        </x-td>
                        <x-td>
                            {{ number_format($salida->costo_unitario_promedio, 2) }}
                        </x-td>
                        <x-td>{{ $salida->tipo_salida }}</x-td>
                        <x-td>{{ $salida->estado }}</x-td>
                    </x-tr>
                @endforeach
            </x-slot>
        </x-table>

        <div class="mt-4">
            {{ $salidas->links() }}
        </div>
    </x-card>
</div>