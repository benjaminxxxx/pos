<template>
  <div class="flex items-center gap-4">
    <button
      :class="[
        'flex items-center p-4 bg-white hover:bg-slate-200 w-full border border-slate-200 focus:outline-none',
        $attrs.class
      ]"
    >
      <i :class="['fa', icon, 'mr-2 w-10 text-3xl text-slate-600']"></i>
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
