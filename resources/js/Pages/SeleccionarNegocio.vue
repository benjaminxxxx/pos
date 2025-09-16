<script setup>
defineProps({ negocios: Array })
</script>

<template>
  <div class="p-6">
    <!-- Solo mostrar el título si hay negocios -->
    <h2 v-if="negocios.length > 0" class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">
      Selecciona un negocio
    </h2>

    <!-- Si hay negocios -->
    <div v-if="negocios.length > 0" class="grid gap-4">
      <button
        v-for="negocio in negocios"
        :key="negocio.id"
        class="w-full text-left bg-white border border-gray-200 shadow-sm rounded-lg px-5 py-3 transition-all
               hover:shadow-md hover:border-blue-400
               dark:bg-gray-800 dark:border-gray-700 dark:hover:border-blue-500"
        @click="$emit('negocioSeleccionado', negocio)"
      >
        <div class="font-semibold text-gray-900 dark:text-gray-100">
          {{ negocio.nombre_legal }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
          {{ negocio.direccion }}
        </div>
      </button>
    </div>

    <!-- Si NO hay negocios -->
    <div v-else class="text-center">
      <a
        href="/mi-tienda/negocios"
        class="inline-block text-white bg-emerald-500 hover:bg-emerald-600 focus:ring-4 
               focus:ring-emerald-300 font-medium rounded-lg text-base px-6 py-3 mt-6 
               shadow-md transition-all
               dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-900"
      >
        🚀 Iniciar a vender: Crear negocio y sucursal
      </a>
    </div>
  </div>
</template>
