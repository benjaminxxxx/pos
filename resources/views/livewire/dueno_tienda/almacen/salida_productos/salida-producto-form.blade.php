<div>
    <x-dialog-modal wire:model.live="mostrarFormularioSalidaProducto">
        <x-slot name="title">
            Registrar Salida de Producto
        </x-slot>

        <x-slot name="content">
            <form wire:submit="guardarSalidaProducto" id="frmRegistroSalidaProductos"
                class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Fecha de entrada --}}
                <flux:input label="Fecha de salida" id="fecha_salida" type="date" wire:model="form.fecha_salida"
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

                <flux:select label="Motivo de Salida" id="tipo_salida" wire:model="form.tipo_salida"
                    placeholder="Seleccione un motivo">
                    <option value="POR AJUSTE DE INVENTARIO">POR AJUSTE DE INVENTARIO</option>
                    <option value="POR MERMA O VENCIMIENTO">POR MERMA O VENCIMIENTO</option>
                    <option value="POR CONSUMO INTERNO">POR CONSUMO INTERNO</option>
                    <option value="POR PROMOCIONAL AL CLIENTE">POR PROMOCIONAL AL CLIENTE</option>
                    <option value="POR PRÉSTAMO O MUESTRA">POR PRÉSTAMO O MUESTRA</option>
                    <option value="POR DEVOLUCIÓN A PROVEEDOR">POR DEVOLUCIÓN A PROVEEDOR</option>
                    <option value="POR TRASLADO A SUCURSAL">POR TRASLADO A SUCURSAL</option>
                    <option value="POR ERROR DE REGISTRO">POR ERROR DE REGISTRO</option>
                </flux:select>

            </form>
        </x-slot>

        <x-slot name="footer">
            <x-button variant="secondary" wire:click="$set('mostrarFormularioSalidaProducto', false)"
                wire:loading.attr="disabled">
                Cancelar
            </x-button>
            <x-button type="submit" form="frmRegistroSalidaProductos">
                <i class="fa fa-save"></i> Registrar Salida
            </x-button>
        </x-slot>
    </x-dialog-modal>
    <x-loading wire:loading />
</div>