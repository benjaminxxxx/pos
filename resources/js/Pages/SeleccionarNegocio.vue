<script setup>
defineProps({ negocios: Array })
</script>

<template>
  <div class="p-6">
    <h2 class="text-xl font-bold mb-4">Selecciona un negocio</h2>
    <div class="grid gap-4">
      <button
        v-for="negocio in negocios"
        :key="negocio.id"
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
        @click="$emit('negocioSeleccionado', negocio)"
      >
        <div class="font-medium">{{ negocio.nombre_legal }}</div>
        <div class="text-sm text-gray-500 dark:text-gray-200">{{ negocio.direccion }}</div>
      </button>
    </div>
  </div>
</template>
