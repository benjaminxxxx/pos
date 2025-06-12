<template>
  <div class="overflow-x-auto">
    <table class="min-w-full table-auto">
      <thead>
        <tr>
          <th class="text-left p-2">Producto</th>
          <th class="text-right p-2">Valor Unitario</th>
          <th class="text-right p-2">IGV</th>
          <th class="text-right p-2">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in productos" :key="index" class="border-t">
          <!-- Descripción -->
          <td class="p-2">
            <div class="font-semibold">[{{ item.unidad }}] {{ item.descripcion }} x{{ parseInt(item.cantidad) }}</div>
            <div class="text-sm text-gray-500">Categoría: {{ item.categoria_producto }}</div>
          </td>

          <!-- Valor unitario -->
          <td class="p-2 text-right">
            S/. {{ parseFloat(item.monto_valor_unitario).toFixed(2) }}
          </td>

          <!-- IGV -->
          <td class="p-2 text-right">
            S/. {{ parseFloat(item.igv).toFixed(2) }}
          </td>

          <!-- Subtotal (valor de venta) -->
          <td class="p-2 text-right">
            S/. {{ parseFloat(item.monto_valor_venta).toFixed(2) }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  productos: Array
});
</script>
