<x-card>
    <x-card-title>
        <i class="fas fa-triangle-exclamation text-red-600 text-lg"></i>
        Alertas de Inventario
    </x-card-title>

    <div class="space-y-3 mt-4">
        @foreach ($alertas_inventario as $alert)
            <x-card2 class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas fa-box text-gray-500 text-sm dark:text-white"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $alert['product'] }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-300">{{ $alert['stock'] }} unidades</p>
                    </div>
                </div>

                <span
                    class="text-xs px-2 py-1 rounded-md font-semibold
                            {{ $alert['status'] === 'critical' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $alert['status'] === 'critical' ? 'Crítico' : 'Bajo' }}
                </span>
            </x-card2>
        @endforeach
    </div>
</x-card>