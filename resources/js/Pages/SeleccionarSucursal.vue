<script setup>
import { ref, onMounted } from 'vue'
import api from '@/lib/axios'

const sucursales = ref([])
const cargando = ref(true)

const obtenerSucursales = async () => {
    try {
        const response = await api.get('/mis-sucursales')
        sucursales.value = response.data
    } catch (error) {
        console.error('Error cargando sucursales:', error)
    } finally {
        cargando.value = false
    }
}


onMounted(() => {
    obtenerSucursales()
})
</script>


<template>
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Selecciona una sucursal</h2>
        <div v-if="cargando">Cargando...</div>
        <div v-else class="grid gap-4">
            <button v-for="sucursal in sucursales" :key="sucursal.id"
                class="bg-white border rounded p-4 shadow hover:bg-gray-50"
                @click="$emit('sucursalSeleccionada', {id:sucursal.id,nombre:sucursal.nombre})">
                <div class="font-medium">{{ sucursal.nombre }}</div>
                <div class="text-sm text-gray-500">{{ sucursal.direccion }}</div>
            </button>
        </div>
    </div>
</template>
