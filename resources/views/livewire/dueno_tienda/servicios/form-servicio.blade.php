<div>
    <form wire:submit.prevent="guardar">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            <flux:input wire:model="codigo" label="Código" placeholder="Código del servicio" />
            <flux:input wire:model="nombre_servicio" label="Nombre del Servicio" placeholder="Nombre del servicio" />
            <flux:textarea wire:model="descripcion" label="Descripción" placeholder="Descripción del servicio" rows="3" />
            <flux:input wire:model="precio" label="Precio" type="number" step="0.01" placeholder="0.00" />
            <flux:input wire:model="igv" label="IGV (%)" type="number" step="0.01" placeholder="0.00" />
            <flux:select wire:model="categoria_id" label="Categoría">
                <option value="">Seleccione una categoría</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->descripcion }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model="sucursal_id" label="Sucursal">
                <option value="">Seleccione una sucursal</option>
                @foreach ($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                @endforeach
            </flux:select>

            <flux:radio.group wire:model="activo" label="Estado">
                <flux:radio value="1" label="Activo" />
                <flux:radio value="0" label="Inactivo" />
            </flux:radio.group>
        </div>

        <div class="flex justify-end mt-6 space-x-2">
            <flux:button type="button" wire:click="cancelar">
                Cancelar
            </flux:button>

            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="guardar">
                {{ $servicio_id ? 'Actualizar' : 'Guardar' }}
            </flux:button>
        </div>
    </form>
</div>
