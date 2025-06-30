<template>
    <div class="space-y-4">
        <div class="relative w-full max-w-md">
            <i class="fa fa-search absolute top-1/2 left-3 transform -translate-y-1/2 text-gray-400"></i>
            <input ref="inputRef" v-model="busqueda" @input="buscarProductos" placeholder="Buscar producto..." class="pl-10 w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 text-gray-900 text-sm
           focus:outline-none input-energy" />
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
import { ref,onMounted  } from 'vue'
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