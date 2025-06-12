<template>
  <Flex class="mb-2 justify-between">
    <div>
      <p class="font-bold">{{ nombre }}</p>
      <p class="text-sm text-gray-600">{{ documento }}</p>
    </div>
  </Flex>
</template>

<script setup>
import Flex from "@/components/ui/Flex.vue";

const props = defineProps({
  nombre: String,
  documento: String,
});
</script>
