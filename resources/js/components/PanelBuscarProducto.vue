<template>
    <div class="space-y-4 overflow-y-auto h-full pretty-scroll">

        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input ref="inputRef" type="search" id="default-search" v-model="busqueda" @input="buscarProductos"
                class="input-energy block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Buscar producto..." required />
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
import { ref, onMounted } from 'vue'
import api from '@/lib/axios'
import Producto from '@/components/Producto.vue'
import Input from '@/components/ui/Input.vue'

const busqueda = ref('')
const inputRef = ref(null)
const resultados = ref([])
const cargando = ref(false)
onMounted(() => inputRef.value?.focus())
const buscarProductos = async () => {
    const texto = busqueda.value.trim()
    if (texto.length < 1) {
        resultados.value = []
        return
    }

    cargando.value = true
    let negocio = null
    let sucursal = null

    try {
        negocio = JSON.parse(localStorage.getItem('negocioSeleccionado'))
        sucursal = JSON.parse(localStorage.getItem('sucursalSeleccionada'))
    } catch (e) {
        console.error('Error al parsear localStorage:', e)
    }

    // validar negocio (obligatorio)
    if (!negocio || !negocio.id) {
        alert('Debe seleccionar un negocio antes de continuar')
        return
    }

    // validar sucursal (opcional, solo si existe en localStorage)
    if (sucursal && !sucursal.id) {
        alert('Sucursal inválida')
        return
    }
    try {

        const params = {
            negocio_id: negocio.id,
            q: texto
        }

        if (sucursal && sucursal.id) {
            params.sucursal_id = sucursal.id
        }

        const { data } = await api.get('/mis-productos', { params })

        resultados.value = data
    } catch (error) {
        console.error('Error al buscar productos:', error)
        resultados.value = []
    } finally {
        cargando.value = false
    }
}

const emit = defineEmits(['productoSeleccionado'])

function focusYLimpiar() {
    busqueda.value = '';
    inputRef.value?.focus()
}

defineExpose({ focus, focusYLimpiar })

const seleccionarProducto = (producto) => {
    emit('productoSeleccionado', producto)
}
</script>
<style>
@keyframes energyPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
    }

    70% {
        box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

.input-energy:focus {
    animation: energyPulse 1s infinite;
    border-color: #3b82f6;
    outline: none;
}
</style>