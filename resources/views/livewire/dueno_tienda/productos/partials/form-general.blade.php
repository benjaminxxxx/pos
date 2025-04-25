<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <flux:input wire:model="codigo_barra" label="Código de Barras" type="text" />
    <flux:input wire:model="nombre_producto" label="Nombre del Producto" type="text" />
    <flux:textarea wire:model="descripcion" label="Descripción (opcional)" rows="3" />
    <flux:input wire:model="sunat_code" label="Código SUNAT (opcional)" type="text" />
    <flux:select wire:model="categoria_id" label="Categoría">
        <option value="">Seleccione una categoría</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}">{{ $categoria->descripcion }}</option>
        @endforeach
    </flux:select>
    <flux:select wire:model.live="tipo_afectacion_igv" label="Tipo de afectación IGV">
        <option value="">Seleccione un tipo de afectación</option>
        <option value="gravada">Gravado - Operación Onerosa</option>
        <option value="exonerada">Exonerado - Operación Onerosa</option>
        <option value="inafecta">Inafecto - Operación Onerosa</option>
        <option value="exportacion">Exportación</option>
        <option value="gratuita">Gratuito</option>
    </flux:select>
    <flux:select wire:model="igv" label="IGV (%)">
        <option value="">Seleccione IGV según el tipo de afectación</option>
        @if ($tipo_afectacion_igv == 'gravada' || $tipo_afectacion_igv == 'gratuita')
            <option value="18.00">18%</option>
            <option value="10.00">10%</option>
        @else
            <option value="0.00">0%</option>
        @endif
    </flux:select>
    <flux:select wire:model="marca_id" label="Marca">
        <option value="">Seleccione una marca</option>
        @foreach ($marcas as $marca)
            <option value="{{ $marca->id }}">{{ $marca->descripcion_marca }}</option>
        @endforeach
    </flux:select>
    <flux:input wire:model="precio_base" label="Precio Final de venta" type="number" step="0.01" />
    <flux:input wire:model="precio_compra" label="Precio de Compra" type="number" step="0.01" />
    <flux:radio.group wire:model="activo" label="Estado">
        <flux:radio value="1" label="Activo" />
        <flux:radio value="0" label="Inactivo" />
    </flux:radio.group>
</div>
