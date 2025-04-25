<script setup>
import { ref } from 'vue'
import SeleccionarSucursal from '@/Pages/SeleccionarSucursal.vue'
import PanelVender from '@/Pages/PanelVender.vue'

// Propósito: Este componente maneja la lógica para seleccionar una sucursal,
// mostrar el panel de ventas si ya hay una sucursal seleccionada, y permitir
// cambiar la sucursal cuando sea necesario.

const sucursalSeleccionada = ref(localStorage.getItem('sucursalSeleccionada'))

const actualizarSucursal = (id) => {
  sucursalSeleccionada.value = id
  localStorage.setItem('sucursalSeleccionada', id)
}

const cambiarSucursal = () => {
  localStorage.removeItem('sucursalSeleccionada')
  sucursalSeleccionada.value = null
}
</script>

<template>
  <div>
    <SeleccionarSucursal 
      v-if="!sucursalSeleccionada" 
      @sucursalSeleccionada="actualizarSucursal" 
    />

    <PanelVender 
      v-else 
      @mostrarModalSeleccionSucursal="cambiarSucursal" 
    />
  </div>
</template>
