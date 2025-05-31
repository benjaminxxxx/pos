<div x-data="{ expandido: false }" class="space-y-4">

  <!-- Campos básicos siempre visibles -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <flux:input wire:model="descripcion" label="Nombre del Producto" type="text" />

    <flux:select wire:model="unidad" label="Unidad">
      <option value="">Seleccione una unidad</option>
      @foreach ($unidades as $unidad)
        <option value="{{ $unidad->codigo }}">{{ $unidad->descripcion }} ({{ $unidad->alt }})</option>
      @endforeach
    </flux:select>

    <flux:select wire:model.live="tipo_afectacion_igv" label="Tipo de Afectación IGV">
      <option value="">Seleccione un tipo de afectación</option>
      @foreach ($afectaciones as $afectacion)
        <option value="{{ $afectacion->codigo }}">{{ $afectacion->descripcion }}</option>
      @endforeach
    </flux:select>

    <flux:select wire:model="porcentaje_igv" label="IGV (%)" wire:key="porcentaje_igv">
      <option value="">Seleccione IGV según el tipo de afectación</option>
      @if ($aplicaIgv)
        <option value="18">18%</option>
        <option value="10">10%</option>
      @else
        <option value="0">0%</option>
      @endif
    </flux:select>

    <flux:input wire:model="monto_venta" label="Precio Final de Venta (con IGV)" type="number" step="0.01" />
    <flux:input wire:model="monto_compra" label="Precio de Compra (con IGV)" type="number" step="0.01" />
  </div>

  <!-- Botón para expandir/contraer -->
  <flux:button variant="filled" class="mt-4"
    @click="expandido = !expandido" type="button"
  >
    <span x-show="!expandido">Mostrar más opciones</span>
    <span x-show="expandido">Mostrar menos opciones</span>
  </flux:button>

  <!-- Campos adicionales -->
  <div x-show="expandido" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <flux:input wire:model="codigo_barra" label="Código de Barras" type="text" />
    <flux:textarea wire:model="detalle" label="Descripción Detallada (opcional)" rows="3" />

    <flux:input wire:model="sunat_code" label="Código SUNAT (opcional)" type="text" />

    <flux:select wire:model="categoria_id" label="Categoría">
      <option value="">Seleccione una categoría</option>
      @foreach ($categorias as $categoria)
        <option value="{{ $categoria->id }}">{{ $categoria->descripcion }}</option>
      @endforeach
    </flux:select>

    <flux:select wire:model="marca_id" label="Marca">
      <option value="">Seleccione una marca</option>
      @foreach ($marcas as $marca)
        <option value="{{ $marca->id }}">{{ $marca->descripcion_marca }}</option>
      @endforeach
    </flux:select>

    <flux:radio.group wire:model="activo" label="Estado">
      <flux:radio value="1" label="Activo" />
      <flux:radio value="0" label="Inactivo" />
    </flux:radio.group>
  </div>

</div>

