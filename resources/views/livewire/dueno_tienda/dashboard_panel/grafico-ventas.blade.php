<div class="bg-white rounded-xl shadow p-6 h-full">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fas fa-chart-bar text-blue-600"></i>
                Ventas de la Semana
            </h3>
            <p class="text-sm text-gray-600 mt-1">Comparación diaria de ingresos</p>
        </div>
        @if (isset($ventas_semanales['crecimiento']))
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full inline-flex items-center gap-1">
                <i class="fas fa-arrow-up text-blue-600 text-xs"></i>
                {{ $ventas_semanales['crecimiento'] ?? '+0%' }}
            </span>
        @endif
    </div>

    <div class="space-y-4">
        @if (isset($ventas_semanales['datos']))
            @foreach ($ventas_semanales['datos'] as $ventas_semanal)
                @php
                    $maxSales = $ventas_semanales['max'] ?: 1; // evitar división por cero
                    $percentage = ($ventas_semanal['sales'] / $maxSales) * 100;
                @endphp
                <div class="flex items-center gap-4">
                    <div class="w-8 text-sm font-medium text-gray-600">
                        {{ $ventas_semanal['day'] }}
                    </div>
                    <div class="flex-1 flex items-center gap-3">
                        <div class="flex-1 bg-gray-200 rounded-full h-3 relative overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full transition-all duration-500"
                                style="width: {{ $percentage }}%;"></div>
                        </div>
                        <div class="text-sm font-semibold text-gray-900 w-20 text-right">
                            S/. {{ number_format($ventas_semanal['sales'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
    @if (isset($ventas_semanales['total']))
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-900">Total Semanal</span>
                <span class="text-lg font-bold text-blue-900">
                    S/. {{ number_format($ventas_semanales['total'] ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>
    @endif
</div>
