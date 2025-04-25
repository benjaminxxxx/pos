<template>
    <div class="flex justify-between items-center border-b pb-2 mb-4">
      <h2 class="text-xl font-semibold text-gray-800">{{ titulo }}</h2>
      <slot />
    </div>
  </template>
  
  <script setup>
  defineProps({
    titulo: String
  })
  </script>
  