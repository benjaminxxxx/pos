<x-card>
    <x-card-title class="flex items-center gap-2">
        <i class="fas fa-shopping-cart text-green-600 text-lg"></i>
        Accesos Rápidos
    </x-card-title>

    <div class="grid grid-cols-2 gap-3 mt-4">
        @php
            $actions = [
                [
                    'icon' => 'fas fa-plus',
                    'label' => 'Nueva Venta',
                    'color' => 'bg-lime-600 hover:bg-lime-700',
                    'route' => route('ventas'),
                ],
                [
                    'icon' => 'fas fa-box',
                    'label' => 'Inventario',
                    'color' => 'bg-blue-500 hover:bg-blue-600',
                    'route' => route('dueno_tienda.productos'),
                ],
                [
                    'icon' => 'fas fa-users',
                    'label' => 'Clientes',
                    'color' => 'bg-purple-500 hover:bg-purple-600',
                    'route' => route('dueno_tienda.clientes'),
                ]
            ];
        @endphp

        @foreach ($actions as $action)
            <a href="{{ $action['route'] }}"
                class="py-3 flex flex-col items-center justify-center gap-2 hover:scale-105 transition-all {{ $action['color'] }} hover:text-white border-1 border-white/40 rounded-lg text-white text-md font-medium">
                <i class="{{ $action['icon'] }} text-lg"></i>
                <span>{{ $action['label'] }}</span>
            </a>
        @endforeach
    </div>
</x-card>