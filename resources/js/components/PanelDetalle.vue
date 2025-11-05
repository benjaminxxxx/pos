<!-- resources/js/Components/PanelDetalle.vue -->
<template>
    <Modal maxWidth="full">
        <Spacing>
            <div class="flex bg-white text-black w-full" style="max-height:80vh">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Código</th>
                                <th scope="col" class="px-6 py-3">Descripción</th>
                                <th scope="col" class="px-6 py-3">Cantidad</th>
                                <th scope="col" class="px-6 py-3">Valor unitario (sin IGV)</th>
                                <th scope="col" class="px-6 py-3">Precio de venta (con IGV)</th>
                                <th scope="col" class="px-6 py-3">Afectación IGV</th>
                                <th scope="col" class="px-6 py-3">IGV</th>
                                <th scope="col" class="px-6 py-3">Subtotal</th>
                                <th scope="col" class="px-6 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(producto, index) in venta.productos" :key="producto.idUnico"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4">[{{ producto.unidad }}] {{ producto.descripcion }}</td>
                                <td class="px-6 py-4">{{ producto.cantidad }}</td>
                                <td class="px-6 py-4">S/ {{ parseFloat(producto.monto_venta_sinigv).toFixed(2) }}</td>
                                <td class="px-6 py-4">S/ {{ parseFloat(producto.monto_venta).toFixed(2) }}</td>
                                <td class="px-6 py-4">{{ producto.tipo_afectacion_igv }}</td>
                                <td class="px-6 py-4">S/ {{ parseFloat(producto.porcentaje_igv).toFixed(2) }}</td>
                                <td class="px-6 py-4 font-bold">S/ {{ parseFloat(producto.subtotal).toFixed(2) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <Button @click="eliminarProducto(index)" variant="danger">
                                        <i class="fa fa-trash"></i>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Flex class="justify-end mt-4">
                <Button @click="cerrarModal" variant="secondary">Cerrar</Button>
                <Button @click="guardarPrecio">
                    <i class="fa fa-save"></i> Guardar
                </Button>
            </Flex>
        </Spacing>
    </Modal>
</template>

<script setup>
import Button from '@/components/ui/Button.vue'
import Flex from '@/components/ui/Flex.vue'
import Modal from '@/components/ui/Modal.vue'
import Spacing from '@/components/ui/Spacing.vue'

const props = defineProps({
    venta: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['cerrarModal'])
function cerrarModal() {
    emit('cerrarModal')
}
</script>