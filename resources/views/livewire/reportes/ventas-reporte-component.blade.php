<div x-data="ventaReporte">
    <livewire:reportes.ventas-reporte-header-component />

    <div class="mt-5">
        <livewire:reportes.ventas-reporte-filters-component  :filters="$filters" />
        <div class="mt-8">
            <div class="flex gap-2 border-b border-slate-200 overflow-x-auto dark:border-gray-600">
                @foreach ([['id' => 'general', 'label' => 'General', 'icon' => '📊'], ['id' => 'monthly', 'label' => 'Mensual/Anual', 'icon' => '📈'], ['id' => 'branch', 'label' => 'Por Sucursal', 'icon' => '🏢'], ['id' => 'product', 'label' => 'Por Producto', 'icon' => '📦']] as $tab)
                    <button wire:click="$set('activeTab', '{{ $tab['id'] }}')"
                        :class="tab === '{{ $tab['id'] }}'
                            ?
                            'border-blue-500 text-blue-600' :
                            'border-transparent text-slate-600 hover:text-slate-900 dark:text-gray-300 dark:hover:text-gray-200'"
                        class="px-4 py-3 font-medium whitespace-nowrap border-b-2 transition-colors">

                        <span class="mr-2">{{ $tab['icon'] }}</span> {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>

            <div class="mt-6">
                {{-- Renderizado condicional de reportes --}}
                @if ($activeTab === 'general')
                    <livewire:reportes.ventas-reporte-general-component :filters="$filters" wire:key="gen-rep" />
                @elseif($activeTab === 'monthly')
                    <livewire:reportes.ventas-reporte-mensual-component :filters="$filters" wire:key="mon-rep" />
                @elseif($activeTab === 'branch')
                    <livewire:reportes.ventas-reporte-porsucursal-component :filters="$filters" wire:key="bra-rep" />
                @elseif($activeTab === 'product')
                    <livewire:reportes.ventas-reporte-porproducto-component :filters="$filters" wire:key="pro-rep" />
                @endif
            </div>
        </div>
    </div>

</div>
@script
    <script>
        Alpine.data('ventaReporte', () => ({
            tab: @entangle('activeTab'),
            init() {

            }
        }));
    </script>
@endscript
