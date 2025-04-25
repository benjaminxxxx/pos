<template>
  <div class="overflow-x-auto">
    <table class="min-w-full table-auto">
      <thead>
        <tr>
          <th class="text-left p-2">Producto</th>
          <th class="text-center p-2">Cantidad</th>
          <th class="text-right p-2">Subtotal</th>
          <th class="text-center p-2">-</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in productos" :key="index" class="border-t">
          <!-- Nombre del producto -->
          <td class="p-2">
            <div class="font-semibold">{{ item.nombre }}</div>
            <div class="text-sm text-gray-500">S/. {{ parseFloat(item.precio).toFixed(2) }}</div>
          </td>

          <!-- Cantidad con botones -->
          <td class="p-2 text-center">
            <div class="flex justify-center items-center gap-2">
              <button @click="$emit('quitarCantidad', item)" class="bg-orange-500 text-white px-2 rounded">−</button>
              <span>{{ item.cantidad }}</span>
              <button @click="$emit('agregarCantidad', item)" class="bg-orange-500 text-white px-2 rounded">+</button>
            </div>
          </td>

          <!-- Subtotal con hover para mostrar botón de edición -->
          <td class="p-2 text-right relative group">
            <span>S/. {{ (parseFloat(item.precio) * item.cantidad).toFixed(2) }}</span>

            <!-- Icono de editar precio unitario -->
            <button @click="$emit('editarPrecioUnitario', item)"
              class="absolute right-0 top-1/2 -translate-y-1/2 text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
              title="Editar precio unitario">
              <i class="fa fa-pencil"></i>
            </button>
          </td>

          <!-- Botón eliminar -->
          <td class="p-2 text-center">
            <button @click="$emit('quitarCarrito', item)" class="text-red-500">
              <i class="fa fa-trash"></i>
            </button>
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
defineEmits([
  'quitarCarrito',
  'agregarCantidad',
  'quitarCantidad',
  'editarPrecioUnitario'
]);
</script>
