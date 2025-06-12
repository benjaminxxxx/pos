<script setup>
import { ref } from 'vue'
import SeleccionarSucursal from '@/Pages/SeleccionarSucursal.vue'
import PanelVender from '@/Pages/PanelVender.vue'

// Propósito: Este componente maneja la lógica para seleccionar una sucursal,
// mostrar el panel de ventas si ya hay una sucursal seleccionada, y permitir
// cambiar la sucursal cuando sea necesario.

const sucursalSeleccionada = ref(localStorage.getItem('sucursalSeleccionada'))
const sucursalSeleccionadaNombre = ref(localStorage.getItem('sucursalSeleccionadaNombre'))

const actualizarSucursal = (data) => {
  sucursalSeleccionada.value = data.id
  sucursalSeleccionadaNombre.value = data.nombre
  localStorage.setItem('sucursalSeleccionada', data.id)
  localStorage.setItem('sucursalSeleccionadaNombre', data.nombre)
}

const cambiarSucursal = () => {
  localStorage.removeItem('sucursalSeleccionada')
  localStorage.removeItem('sucursalSeleccionadaNombre')
  sucursalSeleccionada.value = null
  sucursalSeleccionadaNombre.value = null
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
