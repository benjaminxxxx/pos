<x-flux-card>
    <x-flux-card-header>
        <x-flux-card-title class="flex items-center gap-2">
            <i class="fas fa-clock text-blue-600 text-lg"></i>
            Transacciones Recientes
        </x-flux-card-title>
    </x-flux-card-header>

    <x-flux-card-content>
        @php
            $transactions = [
                [
                    'id' => '#001234',
                    'time' => '14:32',
                    'amount' => '$45.50',
                    'method' => 'Tarjeta',
                    'status' => 'completed',
                    'items' => 3,
                ],
                [
                    'id' => '#001235',
                    'time' => '14:28',
                    'amount' => '$12.75',
                    'method' => 'Efectivo',
                    'status' => 'completed',
                    'items' => 2,
                ],
                [
                    'id' => '#001236',
                    'time' => '14:25',
                    'amount' => '$89.20',
                    'method' => 'Tarjeta',
                    'status' => 'completed',
                    'items' => 5,
                ],
                [
                    'id' => '#001237',
                    'time' => '14:20',
                    'amount' => '$23.40',
                    'method' => 'Efectivo',
                    'status' => 'completed',
                    'items' => 1,
                ],
                [
                    'id' => '#001238',
                    'time' => '14:15',
                    'amount' => '$67.80',
                    'method' => 'Tarjeta',
                    'status' => 'pending',
                    'items' => 4,
                ],
            ];
        @endphp

        <div class="space-y-4">
            @foreach ($transactions as $transaction)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        @if ($transaction['method'] === 'Tarjeta')
                            <i class="fas fa-credit-card text-blue-500 text-sm"></i>
                        @else
                            <i class="fas fa-money-bill-wave text-green-500 text-sm"></i>
                        @endif

                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $transaction['id'] }}</p>
                            <p class="text-xs text-gray-600">
                                {{ $transaction['time'] }} • {{ $transaction['items'] }} productos
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ $transaction['amount'] }}</p>
                        <span class="text-xs px-2 py-1 rounded-md font-semibold
                            {{ $transaction['status'] === 'completed'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $transaction['status'] === 'completed' ? 'Completado' : 'Pendiente' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </x-flux-card-content>
</x-flux-card>
