<x-flux-card>
    <x-flux-card-header>
        <x-flux-card-title class="flex items-center gap-2">
            <i class="fas fa-shopping-cart text-green-600 text-lg"></i>
            Accesos Rápidos
        </x-flux-card-title>
    </x-flux-card-header>

    <x-flux-card-content>
        <div class="grid grid-cols-2 gap-3">
            @php
                $actions = [
                    [
                        'icon' => 'fas fa-plus',
                        'label' => 'Nueva Venta',
                        'color' => 'bg-green-500 hover:bg-green-600',
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
                    class="h-16 flex flex-col items-center justify-center gap-2 hover:scale-105 transition-all {{ $action['color'] }} hover:text-white border-2 rounded-lg text-white/90 text-xs font-medium">
                    <i class="{{ $action['icon'] }} text-lg"></i>
                    <span>{{ $action['label'] }}</span>
                </a>
            @endforeach
        </div>
    </x-flux-card-content>
</x-flux-card>
