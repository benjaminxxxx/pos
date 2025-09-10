<template>
  <div class="flex items-center gap-4">
    <button
      :class="[
        'flex items-center p-4 bg-white hover:bg-slate-200 w-full border border-slate-200 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600',
        $attrs.class
      ]"
    >
      <i :class="['fa', icon, 'mr-2 w-10 text-3xl text-slate-600 dark:text-gray-300']"></i>
      {{ label }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  }
})
</script>
