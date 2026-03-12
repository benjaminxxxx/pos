<script setup>
defineProps({ sucursales: Array })

</script>


<template>
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Selecciona una sucursal</h2>
        <div v-if="cargando">Cargando...</div>
        <div v-else class="grid gap-4">
            <button v-for="sucursal in sucursales" :key="sucursal.id"
                class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-card-foreground hover:bg-muted cursor-pointer focus:outline-none bg-card rounded-lg border border-border"
                @click="$emit('sucursalSeleccionada', {id:sucursal.id,nombre:sucursal.nombre})">
                <div class="font-medium dark:text-white">{{ sucursal.nombre }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-300">{{ sucursal.direccion }}</div>
            </button>
        </div>
    </div>
</template>
