<template>
    <div class="flex justify-between items-center border-b pb-2 mb-4 dark:border-gray-600">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ titulo }}</h2>
      <slot />
    </div>
  </template>
  
  <script setup>
  defineProps({
    titulo: String
  })
  </script>
  