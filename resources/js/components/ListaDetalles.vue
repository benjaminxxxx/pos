<template>
  <div class="">
    <table class="min-w-full table-auto">
      <thead>
        <tr>
          <th class="text-left p-2">Producto</th>
          <th class="text-right p-2">Valor Unitario</th>
          <th v-if="!esTicket" class="text-right p-2">IGV</th>
          <th class="text-right p-2">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in venta.detalles" :key="index" class="border-t">
          <!-- Descripción -->
          <td class="p-2">
            <div class="font-semibold">[{{ item.unidad }}] {{ item.descripcion }} x{{ parseInt(item.cantidad) }}</div>
            <div class="text-sm text-gray-500">Categoría: {{ item.categoria_producto }}</div>
          </td>

          <!-- Valor unitario -->
          <td class="p-2 text-right">
            S/. {{ parseFloat(valorUnitario(item)).toFixed(2) }}
          </td>

          <!-- IGV (solo si no es ticket) -->
          <td v-if="!esTicket" class="p-2 text-right">
            S/. {{ parseFloat(item.igv).toFixed(2) }}
          </td>

          <!-- Subtotal -->
          <td class="p-2 text-right">
            S/. {{ parseFloat(subtotal(item)).toFixed(2) }}
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  venta: Object
});
console.log(props.venta);
// Detectar si es ticket (ajusta la condición según tu estructura de datos)
const esTicket = computed(() => props.venta.tipo_comprobante_codigo === "ticket");

// Calcular valores según el tipo de venta
function valorUnitario(item) {
  return esTicket.value 
    ? item.monto_precio_unitario // sin impuestos
    : item.monto_valor_unitario; // con impuestos
}

function subtotal(item) {
  return esTicket.value
    ? item.monto_precio_unitario * parseInt(item.cantidad)
    : item.monto_valor_venta;
}
</script>
