<div x-data="resumenMensual">
    <x-card>
        <!-- Header -->
        <flux:heading size="lg" class="mb-8">
            Resumen Mensual de Movimientos de Caja
        </flux:heading>

        <!-- Filtros -->
        <x-flex>
            <flux:select wire:model.live="year" label="Año">
                @foreach($years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </flux:select>

            <!-- Filtro por Mes -->
            <flux:select wire:model.live="month" label="Mes (Opcional)">
                <option value="">Sin filtro (Ver todo el año)</option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </flux:select>
        </x-flex>

        <!-- Tabla de Doble Entrada -->
        <div class="mb-8">
            <flux:heading size="md" class="my-4">
                {{ $month ? 'Resumen Diario de ' . \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') : 'Resumen Mensual de ' . $year }}
            </flux:heading>

            <x-table>
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="bg-gray-50 dark:bg-gray-800 sticky left-0 z-10 text-left">
                            Concepto / {{ $month ? 'Día' : 'Mes' }}
                        </x-th>
                        @foreach($tablaData as $row)
                            <x-th class="text-center">
                                {{ $row['periodo'] ?? $row['dia'] }}
                            </x-th>
                        @endforeach
                    </x-tr>
                </x-slot>

                <x-slot name="tbody">
                    @if(count($tablaData) > 0)
                        <x-tr>
                            <x-td class="font-semibold text-green-600 bg-gray-50 dark:bg-gray-800 sticky left-0">
                                Ingresos
                            </x-td>
                            @foreach($tablaData as $row)
                                <x-td class="text-right text-green-600">
                                    {{ number_format($row['ingresos'], 2, ',', '.') }}
                                </x-td>
                            @endforeach
                        </x-tr>

                        <x-tr>
                            <x-td class="font-semibold text-red-600 bg-gray-50 dark:bg-gray-800 sticky left-0">
                                Egresos
                            </x-td>
                            @foreach($tablaData as $row)
                                <x-td class="text-right text-red-600">
                                    {{ number_format($row['egresos'], 2, ',', '.') }}
                                </x-td>
                            @endforeach
                        </x-tr>

                        <x-tr class="bg-blue-50/30 dark:bg-blue-900/10">
                            <x-td class="font-bold text-blue-600 bg-gray-50 dark:bg-gray-800 sticky left-0">
                                Margen
                            </x-td>
                            @foreach($tablaData as $row)
                                <x-td class="text-right text-blue-600 font-bold">
                                    {{ number_format($row['diferencia'], 2, ',', '.') }}
                                </x-td>
                            @endforeach
                        </x-tr>
                    @else
                        <x-tr>
                            <x-td colspan="100%" class="text-center text-gray-500 py-4">
                                No hay movimientos para este período
                            </x-td>
                        </x-tr>
                    @endif
                </x-slot>
            </x-table>
        </div>
    </x-card>


    <!-- Gráfico Chart.js -->
    <x-card class="my-8">
        <flux:heading size="md" class="mb-4">
            Gráfico de Ingresos vs Egresos
        </flux:heading>

        <div wire:ignore>
            <canvas id="chartMovimientos" height="80"></canvas>
        </div>
    </x-card>

    <x-loading wire:loading />
</div>

@script
<script>
    Alpine.data('resumenMensual', () => ({
        chartInstance: null, // Usamos este nombre para mayor claridad
        chartData: @js($chartData),

        init() {
            // Inicializamos el gráfico por primera vez
            this.iniciarGrafico();

            // Escuchamos el evento de Livewire
            Livewire.on('regenerarChartMovimiento', (data) => {
                // Actualizamos los datos locales
                this.chartData = data[0];

                // Si ya existe un gráfico, lo destruimos antes de crear el nuevo
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                // Volvemos a renderizar
                this.iniciarGrafico();
            });
        },

        iniciarGrafico() {
            const ctx = document.getElementById('chartMovimientos');
            if (!ctx) return;

            // Creamos la nueva instancia
            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.chartData.labels,
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: this.chartData.ingresos,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        },
                        {
                            label: 'Egresos',
                            data: this.chartData.egresos,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { size: 12 },
                                padding: 20,
                                usePointStyle: true,
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'S/ ' + value.toLocaleString('es-PE');
                                }
                            }
                        }
                    }
                }
            });
        }
    }));
</script>
@endscript