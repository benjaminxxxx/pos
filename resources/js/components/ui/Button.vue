<template>
  <button
    :class="buttonClass"
    class="text-white font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none hover:opacity-80 transition cursor-pointer"
  >
    <slot>{{ label }}</slot> <!-- Usamos el slot para permitir contenido dinámico -->
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: false, // No es obligatorio si usas slot
    default: '' // Por defecto, si no se pasa `label`, se usará un string vacío
  },
  variant: {
    type: String,
    default: 'primary' // Puedes definir un valor por defecto, por ejemplo "primary"
  }
})

const buttonClass = computed(() => {
  // Definir las clases según el valor de variant
  const variantClass = {
    primary: 'bg-primary',
    secondary: 'bg-white !text-gray-600 border border-gray-400 hover:bg-gray-200',
    success: 'bg-purple-600',
    info: 'bg-teal-500',
    warn: 'bg-yellow-500',
    help: 'bg-indigo-500',
    danger: 'bg-red-600',
    contrast: 'bg-black'
  }

  // Aplicar la clase correspondiente según la propiedad `variant`
  return variantClass[props.variant] || variantClass.primary // Usar "primary" como valor predeterminado
})
</script>

<style scoped>
/* Puedes agregar estilos personalizados aquí */
</style>
