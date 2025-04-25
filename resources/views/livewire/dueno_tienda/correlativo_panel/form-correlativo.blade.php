<x-card>
    <flux:heading size="lg" class="mb-6">{{ $isEditing ? 'Editar Correlativo' : 'Nuevo Correlativo' }}</flux:heading>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <flux:select wire:model="tipo_comprobante_codigo" label="Tipo de Comprobante" required>
                    <option value="">Seleccione un tipo</option>
                    @foreach($tiposComprobante as $tipo)
                        <option value="{{ $tipo->codigo }}">{{ $tipo->nombre }} ({{ $tipo->codigo }})</option>
                    @endforeach
                </flux:select>
            </div>

            <div>
                <flux:input wire:model="serie" label="Serie" placeholder="Ej: F001, B001" required />
                <p class="text-xs text-gray-500 mt-1">La serie debe ser única para cada tipo de comprobante</p>
            </div>

            <div>
                <flux:input wire:model="correlativo_actual" type="number" min="0" label="Correlativo Actual" required />
                <p class="text-xs text-gray-500 mt-1">El próximo comprobante será {{ strtoupper($serie ?: 'SERIE') }}-{{ str_pad(($correlativo_actual ?: 0) + 1, 8, '0', STR_PAD_LEFT) }}</p>
            </div>

            <div>
                <flux:checkbox wire:model="estado" label="Activo" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Sucursales que utilizarán este correlativo
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                    @foreach($sucursales as $sucursal)
                        <div class="flex items-center space-x-2">
                            <flux:checkbox 
                                id="sucursal_{{ $sucursal->id }}" 
                                wire:model="sucursal_ids" 
                                value="{{ $sucursal->id }}" 
                                label="{{ $sucursal->negocio->nombre_legal }} - {{ $sucursal->nombre }}" 
                            />
                        </div>
                    @endforeach
                </div>
                @error('sucursal_ids') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-flex-end>
            <flux:button wire:click="cancel" variant="outline" type="button">
                Cancelar
            </flux:button>
            <flux:button type="submit" variant="primary">
                {{ $isEditing ? 'Actualizar' : 'Guardar' }}
            </flux:button>
        </x-flex-end>
    </form>
</x-card>

