<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach ($tarjetas_estadisticas as $stat)
        <div class="bg-white rounded-xl shadow hover:shadow-md transition-shadow p-4">
            <div class="flex items-center justify-between pb-2">
                <h4 class="text-sm font-medium text-gray-600">
                    {{ $stat['title'] }}
                </h4>
                @switch($stat['icon'])
                    @case('dollar')
                        <i class="fas fa-dollar-sign h-4 w-4 {{ $stat['color'] }}"></i>
                    @break

                    @case('cart')
                        <i class="fas fa-shopping-cart h-4 w-4 {{ $stat['color'] }}"></i>
                    @break

                    @case('users')
                        <i class="fas fa-users h-4 w-4 {{ $stat['color'] }}"></i>
                    @break

                    @case('trend')
                        <i class="fas fa-chart-line h-4 w-4 {{ $stat['color'] }}"></i>
                    @break
                @endswitch

            </div>
            <div class="text-2xl font-bold text-gray-900">
                {{ $stat['value'] }}
            </div>
            <div class="flex items-center gap-1 mt-1">
                @if ($stat['trend'] === 'up')
                    <i class="fas fa-arrow-up-right text-green-500 text-xs"></i>
                @else
                    <i class="fas fa-arrow-down-right text-red-500 text-xs"></i>
                @endif

                <span
                    class="text-xs px-2 py-0.5 rounded-full 
                    {{ $stat['trend'] === 'up' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $stat['change'] }}
                </span>
                <span class="text-xs text-gray-500 ml-1">vs ayer</span>
            </div>
        </div>
    @endforeach
</div>
