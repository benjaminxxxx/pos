<script setup>
import { ref, onMounted } from 'vue'
import api from '@/lib/axios'
import SeleccionarNegocio from '@/Pages/SeleccionarNegocio.vue'
import SeleccionarSucursal from '@/Pages/SeleccionarSucursal.vue'
import PanelVender from '@/Pages/PanelVender.vue'
//localStorage.setItem('negocioSeleccionado', null)
//localStorage.setItem('sucursalSeleccionada', null)
const negocioSeleccionado = ref(JSON.parse(localStorage.getItem('negocioSeleccionado')))
const sucursalSeleccionada = ref(JSON.parse(localStorage.getItem('sucursalSeleccionada')))

const negocios = ref([])
const cargando = ref(true)

const obtenerNegocios = async () => {
  try {
    const response = await api.get('/mis-negocios')
    negocios.value = response.data

    // Autoselección si hay solo un negocio
    if (negocios.value.length === 1) {
      seleccionarNegocio(negocios.value[0])
    }
  } catch (error) {
    console.error('Error cargando negocios:', error)
  } finally {
    cargando.value = false
  }
}

const seleccionarNegocio = (negocio) => {
  negocioSeleccionado.value = negocio
  localStorage.setItem('negocioSeleccionado', JSON.stringify(negocio))

  // Verificar sucursales
  if (!negocio.sucursales || negocio.sucursales.length === 0) {
    sucursalSeleccionada.value = null
    localStorage.setItem('sucursalSeleccionada', null)
  } else if (negocio.sucursales.length === 1) {
    seleccionarSucursal(negocio.sucursales[0])
  }
}

const seleccionarSucursal = (sucursal) => {
  sucursalSeleccionada.value = sucursal
  localStorage.setItem('sucursalSeleccionada', JSON.stringify(sucursal))
}

const cambiarNegocio = () => {
  negocioSeleccionado.value = null
  sucursalSeleccionada.value = null
  localStorage.removeItem('negocioSeleccionado')
  localStorage.removeItem('sucursalSeleccionada')
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
      v-else
      :negocio="negocioSeleccionado"
      :sucursal="sucursalSeleccionada"
      @cambiarNegocio="cambiarNegocio"
      @cambiarSucursal="cambiarSucursal"
    />
  </div>
</template>
