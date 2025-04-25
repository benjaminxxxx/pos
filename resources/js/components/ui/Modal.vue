<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 overflow-y-auto"
    @keydown.esc="$emit('update:modelValue', false)"
    tabindex="0"
  >
    <!-- Fondo oscuro -->
    <div
      class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"
      @click="$emit('update:modelValue', false)"
    ></div>

    <!-- Contenedor del modal -->
    <div class="flex items-center justify-center min-h-screen">
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden sm:w-full sm:mx-auto z-50"
        :class="maxWidthClass"
        :id="id"
      >
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: Boolean,
  id: {
    type: String,
    default: () => `modal-${Math.random().toString(36).substring(2, 9)}`
  },
  maxWidth: {
    type: String,
    default: '2xl'
  }
});

const emit = defineEmits(['update:modelValue']);

const maxWidthClass = computed(() => {
  return {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    full: 'sm:max-w-full lg:max-w-screen-lg',
    complete: 'sm:max-w-full lg:max-w-screen-lg 2xl:max-w-screen-2xl'
  }[props.maxWidth] || 'sm:max-w-2xl';
});
</script>
