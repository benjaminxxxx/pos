<template>
    <div class="fixed inset-0 flex justify-center items-center">
        <div class="absolute inset-0 bg-black opacity-50">

        </div>
        <div class="bg-white rounded-lg p-6 w-96 z-50 dark:bg-gray-800">
            <h2 class="text-xl font-semibold mb-4">Cambiar Precio Unitario</h2>
            <div class="my-4">
                <table>
                    <tbody>
                        <tr>
                            <th class="text-left">Producto</th>
                            <td>:{{ producto.descripcion }}</td>
                        </tr>
                        <tr>
                            <th class="text-left">Precio anterior</th>
                            <td>:{{ producto.monto_precio_unitario }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mb-4">
                <label for="nuevoPrecio" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nuevo precio unitario:</label>
                <input id="nuevoPrecio" v-model.number="nuevoPrecio" type="number" step="0.01"
                    class="w-full mt-1 p-2 border rounded-md text-right" />
            </div>

            <Flex class="justify-end">
                <Button @click="cerrarModal" variant="secondary">Cancelar</Button>
                <Button @click="guardarPrecio">
                    <i class="fa fa-save"></i> Guardar
                </Button>
            </Flex>

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import Flex from '@/components/ui/Flex.vue'
import Button from '@/components/ui/Button.vue'

// Recibe el producto seleccionado como un prop
const props = defineProps({
    producto: {
        type: Object,
        required: true,
    }
})

// Ref para almacenar el nuevo precio
const nuevoPrecio = ref(props.producto.precio)

// Emitir eventos
const emit = defineEmits(['cerrarModal', 'guardarPrecio'])

// Función para guardar el precio
function guardarPrecio() {
    emit('guardarPrecio', {
        id: props.producto.idUnico,
        nuevoPrecio: nuevoPrecio.value
    })
    emit('cerrarModal')
}

// Cerrar el modal sin hacer cambios
function cerrarModal() {
    emit('cerrarModal')
}
</script>

<style scoped>
/* Agrega aquí tus estilos personalizados */
</style>