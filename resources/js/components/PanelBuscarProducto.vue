<template>
    <div class="space-y-4">
        <div class="relative w-full max-w-md">
            <i class="fa fa-search absolute top-1/2 left-3 transform -translate-y-1/2 text-gray-400"></i>
            <Input v-model="busqueda" @input="buscarProductos" class="pl-10" placeholder="Buscar producto..." />
        </div>

        <div v-if="resultados.length" class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <Producto v-for="producto in resultados" :key="producto.id" :data="producto"
                @seleccionar="seleccionarProducto" />
        </div>


        <p v-else-if="busqueda && !cargando" class="text-gray-500">No se encontraron productos</p>
        <p v-if="cargando" class="text-blue-500">Buscando...</p>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/lib/axios'
import Producto from '@/components/Producto.vue'
import Input from '@/components/ui/Input.vue'

const busqueda = ref('')
const resultados = ref([])
const cargando = ref(false)

const buscarProductos = async () => {
    const texto = busqueda.value.trim()
    if (texto.length < 1) {
        resultados.value = []
        return
    }

    cargando.value = true
    const sucursalId = localStorage.getItem('sucursalSeleccionada')

    try {
        const { data } = await api.get('/mis-productos', {
            params: {
                sucursal_id: sucursalId,
                q: texto
            }
        })
        
        resultados.value = data
    } catch (error) {
        console.error('Error al buscar productos:', error)
        resultados.value = []
    } finally {
        cargando.value = false
    }
}

const emit = defineEmits(['productoSeleccionado'])

const seleccionarProducto = (producto) => {
    emit('productoSeleccionado', producto)
}
</script>