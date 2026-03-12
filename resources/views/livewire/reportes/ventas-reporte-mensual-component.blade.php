<div class="space-y-6">
    {{-- Tabla de Reporte Mensual --}}
    <flux:card>
        <div class="mb-6">
            <flux:heading size="lg">Reporte Mensual/Anual</flux:heading>
        </div>

        <x-table>
            <x-slot name="thead">
                <x-tr>
                    <x-th>Período</x-th>
                    <x-th align="right">Cant. Ventas</x-th>
                    <x-th align="right">Subtotal</x-th>
                    <x-th align="right">IGV</x-th>
                    <x-th align="right">Total</x-th>
                    <x-th align="right">Ticket Promedio</x-th>
                </x-tr>
            </x-slot>

            <x-slot name="tbody">
                @forelse($months as $month)
                    <x-tr>
                        <x-td class="font-medium text-slate-700 dark:text-white">{{ $month->nombre_mes }}</x-td>
                        <x-td align="right">{{ $month->cantidadTransacciones }}</x-td>
                        <x-td align="right">S/. {{ number_format($month->subtotal, 2) }}</x-td>
                        <x-td align="right">S/. {{ number_format($month->igv, 2) }}</x-td>
                        <x-td align="right" class="font-bold text-slate-900 dark:text-white">
                            S/. {{ number_format($month->totalVentas, 2) }}
                        </x-td>
                        <x-td align="right">S/. {{ number_format($month->ticketPromedio, 2) }}</x-td>
                    </x-tr>
                @empty
                    <x-tr>
                        <x-td colspan="6" class="text-center py-8 text-slate-500">
                            No hay datos para el período seleccionado
                        </x-td>
                    </x-tr>
                @endforelse
            </x-slot>
        </x-table>
    </flux:card>

    {{-- Tendencia de Ventas (Barras) --}}
    <flux:card>
        <div class="mb-6">
            <flux:heading size="lg">Tendencia de Ventas</flux:heading>
        </div>

        <div class="space-y-5">
            @foreach($months as $month)
                @php 
                    $percentage = ($month->totalVentas / $maxValue) * 100;
                @endphp
                <div>
                    <div class="flex justify-between mb-2 items-center">
                        <span class="text-sm font-medium text-slate-700 dark:text-white">{{ $month->nombre_mes }}</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-gray-100">S/. {{ number_format($month->totalVentas, 2) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border border-slate-200">
                        <div
                            class="bg-blue-600 h-full rounded-full transition-all duration-500 shadow-sm"
                            style="width: {{ $percentage }}%"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>
    </flux:card>
</div>