<script setup>
import { ref, onMounted } from 'vue'
import api from '@/lib/axios'
import SeleccionarSucursal from '@/Pages/SeleccionarSucursal.vue'
import PanelVender from '@/Pages/PanelVender.vue'

const negocioActivo = ref(null)
const sucursalSeleccionada = ref(JSON.parse(localStorage.getItem('sucursalSeleccionada')))
const cargando = ref(true)
const error = ref(null)

const obtenerConfiguracionVenta = async () => {
  try {
    // Obtenemos el negocio activo directamente del backend (Auth::user()->negocio_activo)
    const response = await api.get('/mi-negocio-activo')
    negocioActivo.value = response.data

    if (!negocioActivo.value) {
      error.value = 'No tienes un negocio activo. Por favor, configura tu negocio primero.'
      return
    }

    const sucursales = negocioActivo.value.sucursales || []
   
    if (sucursales.length === 0) {
      error.value = 'Este negocio no tiene sucursales registradas.'
      return
    }

    // Si solo hay una sucursal, la seleccionamos automáticamente
    if (sucursales.length === 1) {
      seleccionarSucursal(sucursales[0])
    }
    // Si hay varias, validamos que la que está en localStorage pertenezca al negocio actual
    else if (sucursalSeleccionada.value) {
      const existe = sucursales.some(s => s.id === sucursalSeleccionada.value.id)
      if (!existe) cambiarSucursal()
    }

  } catch (err) {
    console.error('Error cargando configuración:', err)
    error.value = 'Error al conectar con el servidor'
  } finally {
    cargando.value = false
  }
}

const seleccionarSucursal = (sucursal) => {
  sucursalSeleccionada.value = sucursal
  localStorage.setItem('sucursalSeleccionada', JSON.stringify(sucursal))
  error.value = null
}

const cambiarSucursal = () => {
  sucursalSeleccionada.value = null
  localStorage.removeItem('sucursalSeleccionada')
}

onMounted(() => {
  obtenerConfiguracionVenta()
  if (window.ventaDuplicada) {
    localStorage.setItem('ventaDuplicada', JSON.stringify(window.ventaDuplicada))
  }
})
</script>

<template>
  <div class="h-[calc(100vh-4rem)] bg-base">
    <div v-if="cargando" class="flex justify-center p-10">
      <div>
        Cargando panel de ventas...
      </div>
    </div>

    <div v-else-if="error" class="max-w-md mx-auto mt-10">
      <div class="border-red-200 bg-red-50 dark:bg-red-900/20">
        <div class="text-red-700 dark:text-red-400 font-medium">{{ error }}</div>
        <a href="/dueno/negocios" class="mt-4">Ir a configuración</a>
      </div>
    </div>

    <SeleccionarSucursal v-else-if="negocioActivo.sucursales?.length >= 1 && !sucursalSeleccionada"
      :sucursales="negocioActivo.sucursales" @sucursalSeleccionada="seleccionarSucursal" />

    <PanelVender v-else-if="negocioActivo && sucursalSeleccionada" :negocio="negocioActivo"
      :sucursal="sucursalSeleccionada" @cambiarSucursal="cambiarSucursal" />
  </div>
</template>