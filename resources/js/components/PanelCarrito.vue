<!-- resources/js/Components/CartPanel.vue -->
<template>
    <PanelBase defaultClass="w-full xl:w-80 2xl:w-[30rem] h-full bg-white dark:bg-gray-700 flex flex-col justify-between">
        <slot />
    </PanelBase>
</template>

<script setup>
import PanelBase from './PanelBase.vue'
</script>