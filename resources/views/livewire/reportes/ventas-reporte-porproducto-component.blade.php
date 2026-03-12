<div class="space-y-6">
    {{-- Tabla de Desempeño --}}
    <flux:card>
        <div class="mb-6">
            <flux:heading size="lg">Desempeño por Producto</flux:heading>
        </div>

        <x-table>
            <x-slot name="thead">
                <x-tr>
                    <x-th>Producto</x-th>
                    <x-th align="right">Cantidad</x-th>
                    <x-th align="right">Ingresos</x-th>
                    <x-th align="right">Costo</x-th>
                    <x-th align="right">Utilidad</x-th>
                    <x-th align="right">Part. %</x-th>
                </x-tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach($products as $product)
                    @php 
                        $participation = ($product->ingresoTotal / $totalIngresos) * 100;
                    @endphp
                    <x-tr>
                        <x-td class="font-medium text-slate-700 dark:text-white">{{ $product->nombre }}</x-td>
                        <x-td align="right">{{ number_format($product->cantidadVendida, 2) }}</x-td>
                        <x-td align="right" class="font-bold">S/. {{ number_format($product->ingresoTotal, 2) }}</x-td>
                        <x-td align="right" class="text-slate-500 text-xs">S/. {{ number_format($product->costoTotal, 2) }}</x-td>
                        <x-td align="right" class="text-green-600 font-semibold">S/. {{ number_format($product->utilidadTotal, 2) }}</x-td>
                        <x-td align="right" class="text-slate-500">{{ number_format($participation, 1) }}%</x-td>
                    </x-tr>
                @endforeach
            </x-slot>
        </x-table>
    </flux:card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Productos --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">Top 5 Más Vendidos</flux:heading>
            <div class="space-y-3">
                @foreach($products->take(5) as $index => $product)
                    @php $participation = ($product->ingresoTotal / $totalIngresos) * 100; @endphp
                    <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-xl border border-slate-100 dark:bg-gray-700 dark:border-gray-600">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow">
                            <p class="text-sm font-bold text-slate-900 truncate w-48 dark:text-white">{{ $product->nombre }}</p>
                            <p class="text-xs text-slate-500 dark:text-gray-100">{{ number_format($product->cantidadVendida, 0) }} un. - S/. {{ number_format($product->ingresoTotal, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-slate-700 dark:text-gray-200">{{ number_format($participation, 1) }}%</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>

        {{-- Análisis de Utilidad --}}
        <flux:card>
            <flux:heading size="md" class="mb-4">Margen de Utilidad</flux:heading>
            <div class="space-y-5">
                @foreach($products->take(8) as $product)
                    @php 
                        $margen = $product->ingresoTotal > 0 ? ($product->utilidadTotal / $product->ingresoTotal) * 100 : 0;
                        $colorClass = $margen > 30 ? 'bg-green-500' : ($margen > 15 ? 'bg-yellow-500' : 'bg-orange-500');
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1.5 items-end">
                            <span class="text-xs font-bold text-slate-600 uppercase dark:text-gray-300">{{ $product->nombre }}</span>
                            <span class="text-xs font-black {{ $margen > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($margen, 1) }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                            <div class="{{ $colorClass }} h-full transition-all duration-1000" style="width: {{ min(max($margen, 0), 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>
    </div>
</div>