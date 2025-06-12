<template>
    <div class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col hover:shadow-lg transition cursor-pointer">
      <img
        :src="imagen"
        alt="Imagen del producto"
        class="h-44 w-full object-cover"
      />
  
      <div class="p-4 flex flex-col gap-1 grow">
        <p class="font-semibold text-sm text-gray-700 leading-tight truncate-2-lines">
          {{ producto.descripcion }}
        </p>
  
        <p class="text-xs flex items-center gap-1" :class="producto.stock <= 0 ? 'text-orange-600' : 'text-green-700'">
          <i class="fa-solid" :class="producto.stock <= 0 ? 'fa-triangle-exclamation' : 'fa-box'"></i>
          {{ producto.stock <= 0 ? 'Sin stock' : `${producto.stock} en stock` }}
        </p>
  
        <p class="font-bold text-base text-gray-900">
          S/. {{ formatearPrecio(producto.monto_venta) }}
        </p>
  
        <!-- Botón por unidad -->
        <Button @click.stop="$emit('seleccionar', { producto, presentacion: null })" variant="secondary">
          {{producto.unidad_alt}} x1 a s/. {{ formatearPrecio(producto.monto_venta) }}
        </Button>
  
        <!-- Botones por presentación -->
        <div v-if="producto.presentaciones?.length" class="flex flex-col gap-2 mt-2">
          <Button
            v-for="pres in producto.presentaciones"
            :key="pres.id" @click.stop="$emit('seleccionar', { producto, presentacion: pres })">
            {{ pres.unidad_alt }} {{ pres.descripcion }} x{{ pres.factor }} a S/. {{ formatearPrecio(pres.precio) }}
          </Button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import Button from '@/components/ui/Button.vue'
  
  const props = defineProps({
    data: Object
  })
  
  const emit = defineEmits(['seleccionar'])
  
  const producto = props.data
  
  const imagen = producto.imagen_path
    ? `/uploads/${producto.imagen_path}`
    : 'https://placehold.co/170x250?text=Producto'
  
  function formatearPrecio(precio) {
    return Number(precio || 0).toFixed(2)
  }
  </script>
  
  <style scoped>
  .truncate-2-lines {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  </style>
  