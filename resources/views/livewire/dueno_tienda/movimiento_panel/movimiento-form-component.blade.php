<x-card>

    <flux:heading size="lg" class="mb-8">
        Registrar Movimiento de Caja
    </flux:heading>

    {{-- Errores generales --}}
    @if ($errors->any())
        <flux:card class="mb-6 border-red-200 bg-red-50">
            <flux:heading size="sm" class="text-red-900 mb-2">
                Errores en el formulario
            </flux:heading>

            <ul class="list-disc list-inside text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </flux:card>
    @endif

    

    {{-- Mensaje de éxito --}}
    @if (session()->has('success'))
        <flux:card class="mb-6 border-green-200 bg-green-50">
            <p class="text-green-700 text-sm">
                {{ session('success') }}
            </p>
        </flux:card>
    @endif

    <form wire:submit.prevent="guardar">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <flux:select wire:model="form.sucursal_id" required label="Sucursal">
                    <option value="">Seleccionar...</option>
                    @if($sucursales)
                        @foreach ($sucursales as $sucursal)
                            <flux:select.option value="{{ $sucursal->id }}">
                                {{ $sucursal->nombre }}
                            </flux:select.option>
                        @endforeach
                    @endif
                </flux:select>
            </div>
            <div>
                <flux:select label="Tipo de flujo" required wire:model.live="tipoFlujoSeleccionado">
                    <option value="">Seleccionar...</option>
                    <option value="ingreso">Ingreso</option>
                    <option value="egreso">Egreso</option>
                </flux:select>
            </div>

            <div>
                <flux:select label="Tipo de Movimiento" required wire:model="form.tipo_movimiento_id"
                    :disabled="!$tipoFlujoSeleccionado">
                    <option value="">
                        {{ $tipoFlujoSeleccionado
    ? 'Seleccionar tipo...'
    : 'Seleccione primero ingreso o egreso' }}
                    </option>

                    @foreach ($this->tiposMovimiento as $tipo)
                        <option value="{{ $tipo->id }}">
                            {{ $tipo->nombre }} ({{ $tipo->codigo }})
                        </option>
                    @endforeach
                </flux:select>
            </div>


            {{-- Monto --}}
            <div>
                <flux:input type="number" step="0.01" min="0" label="Monto" prefix="S/" required wire:model="form.monto"
                    placeholder="0.00" />
            </div>

            {{-- Método de pago --}}
            <div>
                <flux:input label="Método de pago" wire:model="form.metodo_pago"
                    placeholder="Efectivo, Transferencia, Yape..." />
            </div>

            {{-- Fecha --}}
            <div>
                <flux:input type="datetime-local" label="Fecha y hora" required wire:model="form.fecha" />
            </div>

            {{-- Observación --}}
            <div class="md:col-span-2">
                <flux:textarea label="Observación" rows="4" wire:model="form.observacion"
                    placeholder="Detalles adicionales del movimiento..." />
            </div>

        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-4 pt-6">

            <flux:button variant="ghost" href="{{ route('dueno_tienda.movimientos') }}">
                Ver Movimientos
            </flux:button>

            <flux:button type="submit" variant="primary">
                Registrar Movimiento
            </flux:button>

        </div>
    </form>
    <x-loading wire:loading />
</x-card>