<!-- resources/js/Components/PanelBase.vue -->
<template>
    <div :class="defaultClass">
        <slot />
    </div>
</template>

<script setup>
defineProps({
    defaultClass: {
        type: String,
        default: '',
    }
})
</script>