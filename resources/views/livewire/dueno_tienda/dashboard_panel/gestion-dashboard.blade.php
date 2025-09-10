<div>
    <x-loading wire:loading />
    <div class="flex flex-col space-y-6 p-6">
        @include('livewire.dueno_tienda.dashboard_panel.encabezado-tablero')

        <div class="grid gap-6">
            {{-- Tarjetas de Estadísticas --}}
            @include('livewire.dueno_tienda.dashboard_panel.tarjetas-estadisticas')

            {{-- Rejilla de Contenido Principal --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Gráfico de Ventas - Ocupa 2 columnas --}}
                <div class="lg:col-span-2">
                    @include('livewire.dueno_tienda.dashboard_panel.grafico-ventas')
                </div>

                {{-- Acciones Rápidas y Alertas de Inventario --}}
                <div class="space-y-6">
                    @include('livewire.dueno_tienda.dashboard_panel.acciones-rapidas')
                    @include('livewire.dueno_tienda.dashboard_panel.alertas-inventario')
                </div>
            </div>

            {{-- 
    Sección Inferior del Dashboard:
    Contiene componentes opcionales como:
    - Productos más vendidos
    - Transacciones recientes
    - Métricas de rendimiento
--}}

            {{--
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @include('livewire.dueno_tienda.dashboard_panel.productos-mas-vendidos')
    @include('livewire.dueno_tienda.dashboard_panel.transacciones-recientes')
    @include('livewire.dueno_tienda.dashboard_panel.metricas-rendimiento')
</div>
--}}

        </div>
    </div>
</div>
