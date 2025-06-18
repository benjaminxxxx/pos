<x-flux-card>
    <x-flux-card-header>
        <x-flux-card-title class="flex items-center gap-2">
            <i class="fas fa-triangle-exclamation text-red-600 text-lg"></i>
            Alertas de Inventario
        </x-flux-card-title>
    </x-flux-card-header>

    <x-flux-card-content>
        <div class="space-y-3">
            @foreach ($alertas_inventario as $alert)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-box text-gray-500 text-sm"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $alert['product'] }}</p>
                            <p class="text-xs text-gray-600">{{ $alert['stock'] }} unidades</p>
                        </div>
                    </div>

                    <span class="text-xs px-2 py-1 rounded-md font-semibold
                        {{ $alert['status'] === 'critical' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $alert['status'] === 'critical' ? 'Crítico' : 'Bajo' }}
                    </span>
                </div>
            @endforeach
        </div>
    </x-flux-card-content>
</x-flux-card>
