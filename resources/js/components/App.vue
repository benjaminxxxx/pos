<script setup>
import { ref, onMounted } from 'vue'
import api from '@/lib/axios'
import SeleccionarNegocio from '@/Pages/SeleccionarNegocio.vue'
import SeleccionarSucursal from '@/Pages/SeleccionarSucursal.vue'
import PanelVender from '@/Pages/PanelVender.vue'

const negocioSeleccionado = ref(JSON.parse(localStorage.getItem('negocioSeleccionado')))
const sucursalSeleccionada = ref(JSON.parse(localStorage.getItem('sucursalSeleccionada')))
const negocios = ref([])
const cargando = ref(true)
const error = ref(null)

const obtenerNegocios = async () => {
  try {
    const response = await api.get('/mis-negocios')
    negocios.value = response.data

    // Validar si el negocioSeleccionado todavía existe
    if (
      negocioSeleccionado.value &&
      !negocios.value.some(n => n.id === negocioSeleccionado.value.id)
    ) {
      cambiarNegocio()
    }

    if (negocios.value.length === 1) {
      seleccionarNegocio(negocios.value[0])
    }
  } catch (err) {
    console.error('Error cargando negocios:', err)
    error.value = 'No se pudieron cargar los negocios'
  } finally {
    cargando.value = false
  }
}

const seleccionarNegocio = (negocio) => {
  negocioSeleccionado.value = negocio
  localStorage.setItem('negocioSeleccionado', JSON.stringify(negocio))

  if (!negocio.sucursales || negocio.sucursales.length === 0) {
    sucursalSeleccionada.value = null
    localStorage.removeItem('sucursalSeleccionada')
    error.value = 'Este negocio no tiene sucursales registradas. Debes crear al menos una para poder vender.'
    return
  }

  if (negocio.sucursales.length === 1) {
    seleccionarSucursal(negocio.sucursales[0])
  } else {
    sucursalSeleccionada.value = null
    localStorage.removeItem('sucursalSeleccionada')
  }
}


const seleccionarSucursal = (sucursal) => {
  sucursalSeleccionada.value = sucursal
  localStorage.setItem('sucursalSeleccionada', JSON.stringify(sucursal))
  error.value = null
}

const cambiarNegocio = () => {
  negocioSeleccionado.value = null
  sucursalSeleccionada.value = null
  localStorage.removeItem('negocioSeleccionado')
  localStorage.removeItem('sucursalSeleccionada')
  error.value = null
}

const cambiarSucursal = () => {
  sucursalSeleccionada.value = null
  localStorage.removeItem('sucursalSeleccionada')
}

onMounted(() => {
  obtenerNegocios()
})
</script>

<template>
  <div>
    <div v-if="cargando">Cargando...</div>

    <!-- Error si negocio no tiene sucursales -->
    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
      <p class="font-semibold">{{ error }}</p>
      <div class="mt-3 flex gap-3">
        <button
          @click="cambiarNegocio"
          class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-3 py-1 rounded"
        >
          Cambiar negocio
        </button>
        <a
          href="/mi-tienda/sucursales"
          class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1 rounded"
        >
          Crear sucursal
        </a>
      </div>
    </div>

    <!-- Paso 1: seleccionar negocio -->
    <SeleccionarNegocio
      v-else-if="!negocioSeleccionado"
      :negocios="negocios"
      @negocioSeleccionado="seleccionarNegocio"
    />

    <!-- Paso 2: seleccionar sucursal (si hay varias) -->
    <SeleccionarSucursal
      v-else-if="negocioSeleccionado && negocioSeleccionado.sucursales?.length > 1 && !sucursalSeleccionada"
      :sucursales="negocioSeleccionado.sucursales"
      @sucursalSeleccionada="seleccionarSucursal"
    />

    <!-- Paso 3: mostrar panel de ventas -->
    <PanelVender
      v-else-if="negocioSeleccionado && sucursalSeleccionada"
      :negocio="negocioSeleccionado"
      :sucursal="sucursalSeleccionada"
      @cambiarNegocio="cambiarNegocio"
      @cambiarSucursal="cambiarSucursal"
    />
  </div>
</template>
