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
        <Modal v-model="formularioRegistrar">
            <form action="#" @submit.prevent="registrarCliente">
                <div class="px-6 py-4">
                    <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Registrar Cliente
                    </div>

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

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <Select v-model="tipoCliente" label="Tipo de Cliente" required>
                                <option value="persona">Persona</option>
                                <option value="empresa">Empresa</option>
                            </Select>
                        </div>
                        <div>
                            <Select v-model="tipoDocumento" label="Tipo de Documento" required>
                                <option value="1">DNI</option>
                                <option value="6">RUC</option>
                                <option value="4">Carnet de Extranjería</option>
                                <option value="7">Pasaporte</option>
                            </Select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de
                                Documento / RUC</label>
                            <div class="flex gap-2">
                                <Input v-model="numeroDocumento" type="text" class="flex-1"
                                    placeholder="Ingrese número de RUC" required />
                                <Button type="button" @click="consultarSunat" v-if="tipoDocumento == 6"
                                    :disabled="isConsultandoSunat">
                                    <template v-if="isConsultandoSunat">
                                        <svg class="animate-spin h-4 w-4 mr-2 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4l5-5-5-5v4a12 12 0 100 24v-4l-5 5 5 5v-4a8 8 0 01-8-8z" />
                                        </svg>
                                        Cargando...
                                    </template>
                                    <template v-else>
                                        Buscar datos en SUNAT
                                    </template>
                                </Button>

                            </div>
                        </div>
                        <div>
                            <Input v-model="nombreCompleto" type="text" label="Nombre Completo / Razón Social"
                                required />
                        </div>
                        <div>
                            <Input v-model="nombreComercial" type="text" label="Nombre Comercial (opcional)" />
                        </div>
                        <div>
                            <Input v-model="direccion" type="text" label="Dirección" />
                        </div>
                        <div>
                            <Input v-model="distrito" type="text" label="Distrito" />
                        </div>
                        <div>
                            <Input v-model="provincia" type="text" label="Provincia" />
                        </div>
                        <div>
                            <Input v-model="departamento" type="text" label="Departamento" />
                        </div>
                        <div>
                            <Input v-model="telefono" type="text" label="Teléfono" />
                        </div>
                        <div>
                            <Input v-model="whatsapp" type="text" label="WhatsApp" />
                        </div>
                        <div>
                            <Input v-model="email" type="email" label="Correo Electrónico" />
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
import Textarea from "@/components/ui/Textarea.vue";

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
const isConsultandoSunat = ref(false);
const tipoCliente = ref("persona");
const nombreCompleto = ref("");
const nombreComercial = ref("");
const numeroDocumento = ref("");
const tipoDocumento = ref("1"); // Por defecto DNI
const errorMensaje = ref("");

const direccion = ref("");
const distrito = ref("");
const provincia = ref("");
const departamento = ref("");
const telefono = ref("");
const whatsapp = ref("");
const email = ref("");

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
        tipo_documento_id: tipoDocumento.value,
        numero_documento: numeroDocumento.value,
        nombre_completo: nombreCompleto.value,
        nombre_comercial: nombreComercial.value,
        direccion: direccion.value,
        distrito: distrito.value,
        provincia: provincia.value,
        departamento: departamento.value,
        telefono: telefono.value,
        whatsapp: whatsapp.value,
        email: email.value,
    };

    try {
        const response = await api.post("/cliente/crear", clienteData);

        emit("clienteSeleccionado", response.data);
        formularioRegistrar.value = false;

        // Limpiar formulario
        tipoCliente.value = "persona";
        tipoDocumento.value = "1";
        numeroDocumento.value = "";
        nombreCompleto.value = "";
        nombreComercial.value = "";
        direccion.value = "";
        distrito.value = "";
        provincia.value = "";
        departamento.value = "";
        telefono.value = "";
        whatsapp.value = "";
        email.value = "";
        errorMensaje.value = "";

    } catch (errorData) {
        errorMensaje.value =
            errorData.response?.data?.message ?? "Error al registrar cliente.";
    }
};

const consultarSunat = async () => {
    // Validar que sea un RUC válido de 11 dígitos
    if (tipoDocumento.value !== "6" || numeroDocumento.value.length !== 11 || !/^\d{11}$/.test(numeroDocumento.value)) {
        errorMensaje.value = "Ingrese un RUC válido de 11 dígitos.";
        return;
    }

    errorMensaje.value = "";
    isConsultandoSunat.value = true;

    try {
        const { data } = await api.post('/cliente/sunat', {
            ruc: numeroDocumento.value
        });

        if (data.success) {
            nombreCompleto.value = data.data.nombre_completo;
            nombreComercial.value = data.data.nombre_comercial;
            direccion.value = data.data.direccion;
            departamento.value = data.data.departamento;
            provincia.value = data.data.provincia;
            distrito.value = data.data.distrito;
            telefono.value = data.data.telefono;
        } else {
            errorMensaje.value = data.message || 'No se encontró el RUC.';
        }

    } catch (error) {
        errorMensaje.value = error.response?.data?.message || "Error al consultar SUNAT.";
    } finally {
        isConsultandoSunat.value = false;
    }
};


const cambiarCliente = () => {
    emit("clienteSeleccionado", null);
};
</script>

<style scoped>
/* Aquí puedes agregar estilos personalizados */
</style>