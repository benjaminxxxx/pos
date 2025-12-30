<x-card class="p-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="lg">
            Movimientos de Caja
        </flux:heading>

        <flux:button href="{{ route('dueno_tienda.registrar_movimiento') }}" variant="primary">
            + Nuevo Movimiento
        </flux:button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 my-3">

        <!-- Filtro por tipo de flujo (Ingresos/Egresos) -->
        <flux:select wire:model.live="filtroFlujo" label="Tipo de Flujo">
            <option value="">Todos los tipos</option>
            <option value="ingreso">Solo Ingresos</option>
            <option value="egreso">Solo Egresos</option>
            <option value="neutro">Solo Neutros</option>
        </flux:select>

        <!-- Filtro por tipo de movimiento específico -->
        <flux:select wire:model.live="filtroTipo" label="Tipo de Movimiento">
            <option value="">Todos los movimientos</option>
            @foreach($tiposMovimiento as $tipo)
                <option value="{{ $tipo->id }}">
                    {{ $tipo->nombre }}
                </option>
            @endforeach
        </flux:select>

        <!-- Filtro por fecha -->
        <flux:input wire:model.live="filtroFecha" type="date" label="Fecha" />
    </div>

    <!-- Botón para limpiar filtros -->
    @if($filtroFlujo || $filtroTipo || $filtroFecha)
        <div class="mt-4">
            <flux:button wire:click="limpiarFiltros" variant="subtle" size="sm">
                Limpiar filtros
            </flux:button>

        </div>
    @endif

    @if ($movimientos->isEmpty())

        <!-- Empty state -->
        <x-card class="text-center">
            <p class="text-sm text-gray-500 mb-3">
                No hay movimientos registrados
            </p>

            <flux:button href="{{ route('dueno_tienda.registrar_movimiento') }}">
                Registrar el primer movimiento
            </flux:button>
        </x-card>

    @else

        <!-- Tabla -->
        <x-table>

            <x-slot name="thead">
                <x-tr>
                    <x-th class="text-center">Flujo</x-th>
                    <x-th class="text-center">Fecha</x-th>
                    <x-th class="text-center">Tipo</x-th>
                    <x-th>Sucursal</x-th>
                    <x-th class="text-center">Monto</x-th>
                    <x-th class="text-center">Usuario</x-th>
                </x-tr>
            </x-slot>

            <x-slot name="tbody">
                @foreach ($movimientos as $movimiento)
                    <x-tr>
                        <x-td class="text-center">
                            {{ mb_strtoupper($movimiento->tipoMovimiento->tipo_flujo) }}
                        </x-td>
                        <x-td class="text-center">
                            {{ $movimiento->fecha }}
                        </x-td>

                        <x-td class="text-center">
                            <span class="inline-flex items-center w-fit px-3 py-1 rounded-full text-xs font-semibold
                                                                                @if ($movimiento->tipoMovimiento->tipo_flujo === 'ingreso')
                                                                                    bg-green-100 text-green-800
                                                                                @elseif ($movimiento->tipoMovimiento->tipo_flujo === 'egreso')
                                                                                    bg-red-100 text-red-800
                                                                                @else
                                                                                    bg-gray-100 text-gray-800
                                                                                @endif
                                                                            ">
                                {{ $movimiento->tipoMovimiento->nombre }}
                            </span>
                        </x-td>

                        <x-td>
                            {{ $movimiento->sucursal->nombre }}
                        </x-td>

                        <x-td @class([
                            'text-right font-semibold',
                            'text-green-600' => $movimiento->tipoMovimiento->tipo_flujo === 'ingreso',
                            'text-red-600' => $movimiento->tipoMovimiento->tipo_flujo === 'egreso',
                        ])>
                            {{ $movimiento->tipoMovimiento->tipo_flujo === 'ingreso' ? '+' : '-' }}
                            S/ {{ number_format($movimiento->monto, 2, ',', '.') }}
                        </x-td>


                        <x-td class="text-center">
                            {{ $movimiento->usuario->name }}
                        </x-td>

                    </x-tr>
                @endforeach
            </x-slot>

        </x-table>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $movimientos->links() }}
        </div>

    @endif

    <x-loading wire:loading />
</x-card>