<template>
  <component
    :is="href ? 'a' : 'button'"
    :href="href"
    :class="buttonClass"
    class="text-white font-medium text-center rounded-lg text-sm px-5 py-2.5 focus:outline-none hover:opacity-80 transition cursor-pointer"
  >
    <slot>{{ label }}</slot>
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    default: ''
  },
  variant: {
    type: String,
    default: 'primary'
  },
  href: {
    type: String,
    default: null // Si no se pasa, será un botón
  }
})

const buttonClass = computed(() => {
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

  return variantClass[props.variant] || variantClass.primary
})
</script>

<style scoped>
/* Puedes agregar estilos personalizados aquí */
</style>