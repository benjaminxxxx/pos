<div>
    <x-dialog-modal wire:model.live="mostrarFormularioEntradaProducto">
        <x-slot name="title">
            Registrar Entrada de Producto
        </x-slot>

        <x-slot name="content">
            <form wire:submit="guardarEntradaProducto" id="frmRegistroEntradaProductos"
                class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Fecha de entrada --}}
                <flux:input label="Fecha de ingreso" id="fecha_ingreso" type="date" wire:model="form.fecha_ingreso"
                    class="w-full" />
                {{-- Producto --}}
                <flux:select label="Producto" id="producto_id" wire:model="form.producto_id"
                    placeholder="Seleccione un producto">
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->descripcion }}</option>
                    @endforeach
                </flux:select>

                <flux:select label="Sucursal" id="sucursal_id" wire:model="form.sucursal_id"
                    placeholder="Seleccione una sucursal">
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                    @endforeach
                </flux:select>

                <flux:input label="Cantidad" id="cantidad" type="number" min="1" wire:model="form.cantidad" />

                <flux:input id="costo_unitario" type="number" step="0.01" min="0" wire:model="form.costo_unitario"
                    label="Precio de compra (unidad)" />

                <flux:select label="Motivo de Entrada" id="tipo_entrada" wire:model="form.tipo_entrada"
                    placeholder="Seleccione un motivo">
                    <option value="POR INVENTARIO INICIAL">POR INVENTARIO INICIAL</option>
                    <option value="POR AJUSTE DE INVENTARIO">POR AJUSTE DE INVENTARIO</option>
                    <option value="POR PROMOCIONAL DE PROVEEDOR">POR PROMOCIONAL DE PROVEEDOR</option>
                    <option value="POR CANJE DE PROVEEDOR">POR CANJE DE PROVEEDOR</option>
                </flux:select>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-button variant="secondary" wire:click="$set('mostrarFormularioEntradaProducto', false)"
                wire:loading.attr="disabled">
                Cancelar
            </x-button>
            <x-button type="submit" form="frmRegistroEntradaProductos">
                <i class="fa fa-save"></i> Guardar
            </x-button>
        </x-slot>
    </x-dialog-modal>
    <x-loading wire:loading />
</div>