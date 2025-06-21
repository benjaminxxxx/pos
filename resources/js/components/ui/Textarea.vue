<template>
  <label
    v-if="label"
    :for="id"
    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
  >
    {{ label }}
  </label>

  <textarea
    :id="id"
    :value="modelValue"
    @input="updateValue"
    v-bind="attrs"
    :placeholder="placeholder"
    :required="required"
    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
           block w-full px-4 py-2.5 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400
           dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 resize-y"
  ></textarea>
</template>

<script setup>
import { defineProps, defineEmits, useAttrs } from 'vue'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  modelValue: String,
  label: String,
  id: {
    type: String,
    default: () => `textarea-${Math.random().toString(36).substr(2, 9)}`
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  }
})

const attrs = useAttrs()

const updateValue = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>
