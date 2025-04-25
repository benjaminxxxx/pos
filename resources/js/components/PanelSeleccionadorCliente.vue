<template>
    <div>
        <!-- Input de búsqueda de clientes -->
        <Flex v-if="!clienteSeleccionado" class="justify-between relative">
            <!-- Input con ícono -->
            <div class="relative w-full max-w-md">
                <i class="fa fa-search absolute top-1/2 left-3 transform -translate-y-1/2 text-gray-400"></i>
                <Input v-model="busquedaCliente" @input="buscarCliente" class="pl-10" placeholder="Buscar cliente..." />

                <!-- Lista flotante de resultados -->
                <ul v-if="clientesBuscados.length > 0"
                    class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg overflow-y-auto max-h-60 dark:bg-gray-700 dark:border-gray-600">
                    <li v-for="cliente in clientesBuscados" :key="cliente.id" @click="seleccionarCliente(cliente)"
                        class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                        {{ cliente.nombre_completo || cliente.nombre_comercial }}
                    </li>
                </ul>
            </div>

            <!-- Botón para registrar -->
            <Button @click="mostrarFormularioRegistrar" class="text-green-500 whitespace-nowrap">
                <i class="fa fa-plus"></i> Agre. Cliente
            </Button>
        </Flex>


        <!-- Formulario de registro rápido de cliente -->
        <Modal v-model="formularioRegistrar" maxWidth="lg">
            <form action="#" @submit.prevent="registrarCliente">
                <div class="px-6 py-4">
                    <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Registrar Cliente
                    </div>

                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        <div v-if="errorMensaje != ''"
                            class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                            role="alert">
                            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                            </svg>
                            <span class="sr-only">Info</span>
                            <div class="ms-3 text-sm font-medium">
                                {{ errorMensaje }}
                            </div>
                            <button type="button" @click="() => { errorMensaje = '' }"
                                class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"
                                data-dismiss-target="#alert-2" aria-label="Close">
                                <span class="sr-only">Close</span>
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-3">
                            <Select v-model="tipoCliente" label="Tipo de cliente">
                                <option value="persona">Persona</option>
                                <option value="empresa">Empresa</option>
                            </Select>
                        </div>
                        <div v-if="tipoCliente === 'persona'" class="mt-3">
                            <Input v-model="nombreCompleto" type="text" label="Nombre Completo" required />
                        </div>

                        <div v-if="tipoCliente === 'empresa'" class="mt-3">
                            <Input v-model="nombreComercial" type="text" label="Nombre Comercial" required />
                        </div>
                        <div class="mt-3">
                            <Input v-model="numeroDocumento" type="text" label="Número de Documento" required />
                        </div>
                        <div class="mt-3">
                            <Select v-model="tipoDocumento" label="Tipo de documento">
                                <option value="1">DNI</option>
                                <option value="6">RUC</option>
                                <option value="4">Carnet de Extranjería</option>
                                <option value="7">Pasaporte</option>
                            </Select>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-end">
                    <Flex class="justify-end mt-5">
                        <Button @click="() => { formularioRegistrar = false }" variant="secondary">
                            Cancelar
                        </Button>
                        <Button type="submit">
                            <i class="fa fa-check-circle"></i> Registrar Cliente
                        </Button>
                    </Flex>
                </div>
            </form>
        </Modal>

        <!-- Cliente seleccionado -->
        <Flex v-if="clienteSeleccionado" class="mb-2 justify-between">
            <p>
                {{ clienteSeleccionado.nombre_completo || clienteSeleccionado.nombre_comercial }}
            </p>
            <Button @click="cambiarCliente" variant="success">
                <i class="fa fa-arrows-rotate"></i> Cambiar Cliente
            </Button>
        </Flex>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import api from "@/lib/axios";
import Modal from "@/components/ui/Modal.vue";
import Button from "@/components/ui/Button.vue";
import Flex from "@/components/ui/Flex.vue";
import Input from "@/components/ui/Input.vue";
import Select from "@/components/ui/Select.vue";

const props = defineProps({
    clienteSeleccionado: {
        type: [Object, null],
        default: null,
    },
});

const emit = defineEmits(["clienteSeleccionado"]);

const busquedaCliente = ref("");
const clientesBuscados = ref([]);
const formularioRegistrar = ref(false);
const tipoCliente = ref("persona");
const nombreCompleto = ref("");
const nombreComercial = ref("");
const numeroDocumento = ref("");
const tipoDocumento = ref("1"); // Por defecto DNI
const errorMensaje = ref("");

const buscarCliente = async () => {
    if (busquedaCliente.value.length > 2) {
        try {
            const response = await api.get("/cliente/buscar", {
                params: { busqueda: busquedaCliente.value },
            });
            clientesBuscados.value = response.data;
        } catch (error) {
            console.error(error);
        }
    } else {
        clientesBuscados.value = [];
    }
};

const seleccionarCliente = (cliente) => {
    emit("clienteSeleccionado", cliente);
    busquedaCliente.value = "";
    clientesBuscados.value = [];
};

const mostrarFormularioRegistrar = () => {
    errorMensaje.value = '';
    formularioRegistrar.value = true;
};

const registrarCliente = async () => {
    const clienteData = {
        tipo_cliente_id: tipoCliente.value,
        numero_documento: numeroDocumento.value,
        tipo_documento_id: tipoDocumento.value,
        ...(tipoCliente.value === "persona"
            ? { nombre_completo: nombreCompleto.value }
            : { nombre_comercial: nombreComercial.value }),
    };
    console.log(clienteData);
    try {
        const response = await api.post("/cliente/crear", clienteData);
        emit("clienteSeleccionado", response.data);
        formularioRegistrar.value = false;

        // limpiar formulario
        nombreCompleto.value = "";
        nombreComercial.value = "";
        numeroDocumento.value = "";
        tipoDocumento.value = "1";
    } catch (errorData) {
        errorMensaje.value = errorData.response.data.message;
        console.log(errorData.response.data.message);
    }
};

const cambiarCliente = () => {
    emit("clienteSeleccionado", null);
};
</script>

<style scoped>
/* Aquí puedes agregar estilos personalizados */
</style>