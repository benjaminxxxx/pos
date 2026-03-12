<div class="space-y-6">
    {{-- Tabla de Desempeño --}}
    <flux:card>
        <div class="mb-6">
            <flux:heading size="lg">Desempeño por Sucursal</flux:heading>
        </div>

        <x-table>
            <x-slot name="thead">
                <x-tr>
                    <x-th>Sucursal</x-th>
                    <x-th align="right">Cant. Ventas</x-th>
                    <x-th align="right">Total</x-th>
                    <x-th align="right">Participación %</x-th>
                    <x-th align="right">Ticket Promedio</x-th>
                </x-tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach($branches as $branch)
                    @php 
                        $participation = ($branch->totalVentas / $totalGeneral) * 100;
                    @endphp
                    <x-tr>
                        <x-td class="font-medium text-slate-700 dark:text-white">{{ $branch->nombre }}</x-td>
                        <x-td align="right">{{ $branch->cantidadTransacciones }}</x-td>
                        <x-td align="right" class="font-bold text-slate-900 dark:text-white">
                            S/. {{ number_format($branch->totalVentas, 2) }}
                        </x-td>
                        <x-td align="right">{{ number_format($participation, 1) }}%</x-td>
                        <x-td align="right">S/. {{ number_format($branch->ticketPromedio, 2) }}</x-td>
                    </x-tr>
                @endforeach
            </x-slot>
        </x-table>
    </flux:card>

    {{-- Gráfico de Participación --}}
    <flux:card>
        <div class="mb-6">
            <flux:heading size="lg">Participación de Ventas</flux:heading>
        </div>

        <div class="space-y-6">
            @php
                $colors = ['bg-blue-500', 'bg-purple-500', 'bg-green-500', 'bg-orange-500', 'bg-red-500', 'bg-indigo-500'];
            @endphp
            @foreach($branches as $index => $branch)
                @php 
                    $participation = ($branch->totalVentas / $totalGeneral) * 100;
                    $color = $colors[$index % count($colors)];
                @endphp
                <div>
                    <div class="flex justify-between mb-2 items-center">
                        <span class="text-sm font-medium text-slate-700 dark:text-white">{{ $branch->nombre }}</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ number_format($participation, 1) }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 border border-slate-200 overflow-hidden">
                        <div
                            class="{{ $color }} h-full rounded-full transition-all duration-700 shadow-sm"
                            style="width: {{ $participation }}%"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>
    </flux:card>

    {{-- Ranking de Sucursales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($branches as $index => $branch)
            <flux:card>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-3xl font-black text-slate-300">#{{ $index + 1 }}</p>
                        <p class="text-sm font-bold text-slate-700 mt-1 uppercase dark:text-gray-200">{{ $branch->nombre }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-blue-600">
                            S/. {{ number_format($branch->totalVentas, 2) }}
                        </p>
                        <p class="text-xs text-slate-500 font-medium dark:text-gray-100">{{ $branch->cantidadTransacciones }} ventas realizadas</p>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>
</div>