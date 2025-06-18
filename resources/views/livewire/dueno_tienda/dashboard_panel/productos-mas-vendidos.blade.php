<x-flux-card>
    <x-flux-card-header>
        <x-flux-card-title class="flex items-center gap-2">
            <i class="fas fa-trophy text-yellow-600 text-lg"></i>
            Productos Más Vendidos
        </x-flux-card-title>
    </x-flux-card-header>

    <x-flux-card-content>
        @php
            $products = [
                [ 'name' => 'Coca Cola 600ml', 'sales' => 45, 'revenue' => '$675.00', 'trend' => '+12%' ],
                [ 'name' => 'Pan Francés', 'sales' => 38, 'revenue' => '$190.00', 'trend' => '+8%' ],
                [ 'name' => 'Leche Entera 1L', 'sales' => 32, 'revenue' => '$480.00', 'trend' => '+15%' ],
                [ 'name' => 'Café Americano', 'sales' => 28, 'revenue' => '$420.00', 'trend' => '+5%' ],
                [ 'name' => 'Agua Mineral', 'sales' => 25, 'revenue' => '$125.00', 'trend' => '+3%' ],
            ];
        @endphp

        <div class="space-y-4">
            @foreach ($products as $index => $product)
                @php
                    $badgeColor = match($index) {
                        0 => 'bg-yellow-100 text-yellow-800',
                        1 => 'bg-gray-100 text-gray-800',
                        2 => 'bg-orange-100 text-orange-800',
                        default => 'bg-blue-100 text-blue-800'
                    };
                @endphp

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $badgeColor }}">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $product['name'] }}</p>
                            <p class="text-xs text-gray-600">{{ $product['sales'] }} vendidos</p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ $product['revenue'] }}</p>
                        <span class="inline-flex items-center gap-1 text-xs border px-2 py-1 rounded-md text-gray-700 bg-white">
                            <i class="fas fa-arrow-up-right text-green-500 text-xs"></i>
                            {{ $product['trend'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-flux-card-content>
</x-flux-card>
