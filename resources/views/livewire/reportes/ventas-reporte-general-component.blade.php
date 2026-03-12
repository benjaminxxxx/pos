<div class="space-y-6">
    {{-- KPIs Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($kpis as $kpi)
            <flux:card class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-slate-600 mb-2 dark:text-gray-200">{{ $kpi['label'] }}</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $kpi['value'] }}</p>
                    </div>
                    <div class="{{ $kpi['color'] }} p-3 rounded-lg shadow-sm">
                        <i class="{{ $kpi['icon'] }} text-xl text-white"></i>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>

    {{-- Tabla de Ventas --}}
    <flux:card class="p-6">
        <flux:heading size="xl">Detalle de Ventas</flux:heading>
        <div class="overflow-x-auto">
            <x-table>
                <x-slot name="thead">
                    <x-tr class="text-left">
                        <x-th>Fecha</x-th>
                        <x-th>Cliente</x-th>
                        <x-th>Comprobante</x-th>
                        <x-th class="text-right">Subtotal</x-th>
                        <x-th class="text-right">IGV</x-th>
                        <x-th class="text-right">Total</x-th>
                        <x-th class="text-center">Estado</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @forelse($sales as $sale)
                        <x-tr class="">
                            <x-td class="">{{ $sale->fecha_emision }}</x-td>
                            <x-td class="">{{ $sale->nombre_cliente }}</x-td>
                            <x-td class="">
                                {{ $sale->serie_comprobante }}-{{ $sale->correlativo_comprobante }}
                            </x-td>
                            <x-td class=" text-right">
                                S/. {{ number_format($sale->sub_total, 2) }}
                            </x-td>
                            <x-td class=" text-right">
                                S/. {{ number_format($sale->monto_igv, 2) }}
                            </x-td>
                            <x-td class="text-right">
                                S/. {{ number_format($sale->monto_importe_venta, 2) }}
                            </x-td>
                            <x-td class="text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $sale->estado === 'completada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($sale->estado) }}
                                </span>
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="7" class="py-8 text-center text-slate-500">No se encontraron ventas con los filtros seleccionados.</x-td>
                        </x-tr>
                    @endforelse
                </x-slot>
            </x-table>
            <div class="mt-3">
                {{ $sales->links() }}
            </div>
        </div>
    </flux:card>

    {{-- Resumen Financiero --}}
        <flux:heading size="xl">Resumen Financiero</flux:heading>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-3">
            <flux:card>
                <p class="text-sm text-slate-600 mb-2 dark:text-gray-200">Subtotal</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                    S/. {{ number_format($totalSales - $totalIGV, 2) }}
                </p>
            </flux:card>
            <flux:card>
                <p class="text-sm text-slate-600 mb-2 dark:text-gray-200">Total IGV</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">S/. {{ number_format($totalIGV, 2) }}</p>
            </flux:card>
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg dark:bg-blue-600 dark:border-blue-500">
                <p class="text-sm text-blue-600 font-medium mb-2 dark:text-gray-200">Monto Total Bruto</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-white">S/. {{ number_format($totalSales, 2) }}</p>
            </div>
        </div>
</div>