<template>
    <Card>
        <PanelProductos>
            <Title titulo="Panel de Ventas">
                <div class="md:flex items-center gap-3">
                    Sucursal: <b>{{ sucursalSeleccionadaNombre }}</b>
                    <Button @click="agregarVenta">
                        <i class="fa fa-plus"></i> Nueva Venta
                    </Button>

                    <Button @click="$emit('mostrarModalSeleccionSucursal', true)">
                        <i class="fa fa-save"></i> Cambiar sucursal
                    </Button>
                </div>
            </Title>

            <PanelBuscarProducto @productoSeleccionado="agregarProducto" />

        </PanelProductos>

        <PanelCarrito>
            <div v-if="ventas.length > 0" class="flex flex-col h-full">
                <Spacing>

                    <PanelSeleccionadorCliente v-if="!ventas[ventaActiva]?.registrado" :clienteSeleccionado="cliente"
                        @clienteSeleccionado="actualizarClienteSeleccionado" />

                    <PanelVisorCliente v-else :documento="ventas[ventaActiva]?.documento_cliente"
                        :nombre="ventas[ventaActiva]?.nombre_cliente" />



                    <Flex class="justify-between my-3">
                        <Button @click="navegarVenta('atras')" :disabled="ventaActiva === 0">
                            <i class="fa fa-chevron-left"></i>
                        </Button>

                        <span>Venta n° {{ ventaActiva + 1 }}</span>

                        <Button @click="navegarVenta('adelante')" :disabled="ventaActiva === ventas.length - 1">
                            <i class="fa fa-chevron-right"></i>
                        </Button>
                    </Flex>
                    <!-- Parte navegable y scrollable -->

                    <div class="flex-1 overflow-y-auto space-y-4">

                        <!-- Mostrar detalles si existen -->
                        <ListaDetalles v-if="ventas[ventaActiva].detalles && ventas[ventaActiva].detalles.length"
                            :productos="ventas[ventaActiva].detalles" />

                        <!-- Si no hay detalles, mostrar productos -->
                        <ListaProductos
                            v-else-if="ventas[ventaActiva].productos && ventas[ventaActiva].productos.length"
                            :productos="ventas[ventaActiva].productos" @quitarCarrito="quitarItem"
                            @agregarCantidad="aumentarCantidad" @quitarCantidad="reducirCantidad"
                            @editarPrecioUnitario="abrirModalPrecioUnitario" />
                    </div>
                </Spacing>
                <Spacing v-if="ventas[ventaActiva]?.registrado">
                    <OpcionesVenta :venta-activa="ventas[ventaActiva]" @nueva-venta="resetearVenta" />

                    <Button @click="agregarVenta" class="w-full mt-5">
                        <i class="fa fa-plus"></i> Nueva Venta
                    </Button>
                </Spacing>

                <Flex class="justify-end mt-4" v-show="false">
                    <Spacing>
                        <Button @click="verDetalle(ventaActiva)">
                            <i class="fa fa-list"></i> Avanzado
                        </Button>
                    </Spacing>
                </Flex>
                <!-- Pie fijo al fondo -->
                <div class="bg-neutral-200 border-t mt-auto">
                    <Spacing>
                        <Flex class="justify-between my-3">
                            <table class="w-full">
                                <tbody>
                                    <tr>
                                        <td>
                                            Sub total
                                        </td>
                                        <td class="text-right">
                                            {{ ventas[ventaActiva].valor_venta }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            IGV
                                        </td>
                                        <td class="text-right">
                                            {{ ventas[ventaActiva].total_impuestos }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-left">
                                            Total
                                        </th>
                                        <th class="text-right">
                                            {{ ventas[ventaActiva].monto_importe_venta }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </Flex>

                        <Flex class="justify-between mt-4">
                            <Button @click="pagar" v-if="!ventas[ventaActiva].registrado">
                                <i class="fa fa-check"></i> Pagar
                            </Button>
                            <Button v-if="!ventas[ventaActiva].registrado" @click="eliminarVenta(ventaActiva)"
                                variant="danger">
                                <i class="fa fa-trash"></i> Eliminar Carrito
                            </Button>
                        </Flex>
                    </Spacing>


                </div>
            </div>
        </PanelCarrito>

        <!-- Mostrar modal solo si hay un producto seleccionado -->
        <ModalCambiarPrecioUnitario v-if="mostrarModalPrecioUnitario" :producto="productoParaEditar"
            @cerrarModal="cerrarModalPrecioUnitario" @guardarPrecio="guardarPrecio" />



        <PanelDetalle :venta="ventas[ventaActiva]" @cerrarModal="estaDetalleAbierto = false"
            v-model="estaDetalleAbierto" />

        <Modal v-model="procesarPago" maxWidth="full">
            <div class="flex h-screen bg-white text-black" style="max-height:90vh">
                <div class="lg:w-[21rem] flex flex-col">
                    <div class="h-1/2 bg-slate-400 overflow-y-auto">
                        <div v-for="(method, key) in metodosPago" :key="key">
                            <ButtonMethodPayment :icon="method.icon" :label="method.label" @click="agregarPago(key)" />
                        </div>
                    </div>
                    <div class="h-1/2 bg-slate-200">
                        <Spacing>
                            <p class="font-medium text-lg">Cliente:</p>
                            <PanelSeleccionadorCliente :clienteSeleccionado="cliente"
                                @clienteSeleccionado="actualizarClienteSeleccionado" />
                            <div class="mt-5">
                                <Input type="date" v-model="fechaEmision" label="Fecha de emisión" />
                            </div>
                        </Spacing>
                    </div>
                </div>
                <div class="flex-1 flex flex-col bg-slate-100">
                    <div class="bg-amber-500  text-3xl text-center text-white">
                        <Spacing>
                            Total: S/. <span class="subtotal">{{ ventaAPagar.monto_importe_venta }}</span>
                        </Spacing>
                    </div>
                    <div class="flex-1 overflow-y-auto flex flex-col justify-center">
                        <Spacing>
                            <div class="space-y-2">
                                <div v-for="(metodo, index) in metodosPagoAgregados" :key="index"
                                    class="flex items-center justify-between p-5 rounded shadow cursor-pointer bg-white">
                                    <div class="flex items-center">
                                        <i :class="metodo.icon" class="mr-2"></i>
                                        <span>{{ metodo.label }}</span>
                                    </div>
                                    <Flex>
                                        <Input type="number" v-model="metodo.amount" class="max-w-[14rem]"
                                            placeholder="Monto" />
                                        <Button variant="danger" @click="eliminarPago(index)">
                                            <i class="fa fa-times"></i>
                                        </Button>
                                    </Flex>
                                </div>
                            </div>

                        </Spacing>
                    </div>
                    <Spacing>
                        <div class="flex items-center mb-4">
                            <input id="default-radio-1" type="radio" value="03" class="w-4 h-4"
                                v-model="tipoComprobante">
                            <Label for="default-radio-1">Boleta</Label>
                        </div>
                        <div class="flex items-center mb-4">
                            <input id="default-radio-2" type="radio" value="01" class="w-4 h-4"
                                v-model="tipoComprobante" />
                            <Label for="default-radio-2">Factura</Label>
                        </div>
                        <!--<div class="flex items-center">
                            <input id="default-radio-3" type="radio" value="ticket" class="w-4 h-4"
                                v-model="tipoComprobante" />
                            <Label for="default-radio-3">Ticket</Label>
                        </div>-->

                    </Spacing>
                    <div class="flex items-center">

                        <button @click="procesarVenta" :disabled="procesandoVenta"
                            class="w-1/2 bg-primary hover:opacity-90 transition cursor-pointer border-0 hover:bg-primaryHoverOpacity text-white focus:outline-none text-2xl flex items-center justify-center">
                            <Spacing>
                                <i v-if="!procesandoVenta" class="fa fa-check mr-2"></i>
                                <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                                {{ procesandoVenta ? 'Procesando...' : 'Procesar Venta' }}
                            </Spacing>
                        </button>
                        <button @click="cerrarPanelPago"
                            class="w-1/2 bg-white cursor-pointer transition border-0 hover:bg-stone-100 text-stone-600 bg-text-white text-2xl flex items-center justify-center">

                            <Spacing>
                                <i class="fa fa-arrow-left mr-2"></i> Regresar
                            </Spacing>
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </Card>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '@/lib/axios'
import PanelProductos from '@/Components/PanelProductos.vue'
import PanelCarrito from '@/Components/PanelCarrito.vue'
import ButtonMethodPayment from '@/components/ui/ButtonMethodPayment.vue'
import Title from '@/Components/ui/Title.vue'
import Button from '@/Components/ui/Button.vue'
import Flex from '@/Components/ui/Flex.vue'
import Card from '@/components/ui/Card.vue'
import Modal from '@/components/ui/Modal.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import PanelBuscarProducto from '@/components/PanelBuscarProducto.vue'
import ListaDetalles from '@/components/ListaDetalles.vue'
import ListaProductos from '@/components/ListaProductos.vue'
import OpcionesVenta from '@/components/OpcionesVenta.vue'
import ModalCambiarPrecioUnitario from '@/components/ModalCambiarPrecioUnitario.vue'
import PanelSeleccionadorCliente from '@/components/PanelSeleccionadorCliente.vue'
import PanelVisorCliente from '@/components/PanelVisorCliente.vue'
import Spacing from '@/components/ui/Spacing.vue'
import PanelDetalle from '@/components/PanelDetalle.vue'
import Swal from 'sweetalert2'

const sucursalSeleccionadaNombre = ref(localStorage.getItem('sucursalSeleccionadaNombre'))

const ventas = ref([])
const ventaActiva = ref(null)
const productoSeleccionado = ref(null)
const cliente = ref(null)
const procesarPago = ref(false)
const estaDetalleAbierto = ref(false)
const ventaAPagar = ref(null)
const fechaEmision = ref(null)
const tipoComprobante = ref('01')
const metodosPago = ref({
    cash: { icon: 'fa-money-bill', label: 'Efectivo', amount: '0' },
    card: { icon: 'fa-credit-card', label: 'Tarjeta', amount: '0' },
    yape: { icon: 'fa-mobile-alt', label: 'Yape', amount: '0' },
    plin: { icon: 'fa-qrcode', label: 'Plin', amount: '0' }
})
// Métodos de pago agregados
const metodosPagoAgregados = ref([]);
const ventaActual = computed(() => ventas.value[ventaActiva] ?? null);
const procesandoVenta = ref(false)
const mostrarModalPrecioUnitario = ref(false);
const productoParaEditar = ref(null);

const abrirModalPrecioUnitario = (item) => {
  productoParaEditar.value = item;
  mostrarModalPrecioUnitario.value = true;
}

const cerrarModalPrecioUnitario = () => {
  mostrarModalPrecioUnitario.value = false;
  productoParaEditar.value = null;
}
// Función para agregar un pago
const agregarPago = (metodoPago) => {
    // Verificar si el método de pago ya ha sido agregado
    const metodoExistente = metodosPagoAgregados.value.some((pago) => pago.label === metodosPago.value[metodoPago].label);

    // Si el método no ha sido agregado, lo agregamos
    if (!metodoExistente) {
        const metodoAgregado = { ...metodosPago.value[metodoPago] };
        metodoAgregado.codigo = metodoPago;
        // Si el método es 'Efectivo' y no se ha agregado previamente, asignamos el precio de venta
        if (metodoPago === 'cash' && metodosPagoAgregados.value.length === 0) {
            metodoAgregado.amount = ventaAPagar.value.monto_importe_venta;

        }

        metodosPagoAgregados.value.push(metodoAgregado);
    }
};

// Función para eliminar un pago
const eliminarPago = (index) => {
    metodosPagoAgregados.value.splice(index, 1); // Eliminar el método de pago seleccionado
};

// Computar el total de los pagos
const totalPago = computed(() => {
    return metodosPagoAgregados.value.reduce((total, metodo) => total + parseFloat(metodo.amount || 0), 0).toFixed(2);
});

// Función para iniciar el proceso de pago
const pagar = () => {
    ventaAPagar.value = ventas.value[ventaActiva.value];
    procesarPago.value = true;
    metodosPagoAgregados.value = [];
    fechaEmision.value = new Date().toISOString().split('T')[0];
    agregarPago('cash');
};

const agregarVenta = async () => {
    if (ventas.value.length == 0) {
        //cargar 10 ultimas ventas
        const sucursalSeleccionada = localStorage.getItem('sucursalSeleccionada') ?? null;
        if (!sucursalSeleccionada) {
            return;
        }

        try {

            const { data } = await api.get('/venta/listar/' + sucursalSeleccionada);

            if (data.success) {

                data.ventas.map(venta => {
                    venta.registrado = true;
                    ventas.value.push(venta)
                });
            }

        } catch (error) {
            const msg = error.response?.data?.message || 'Error desconocido';

            Swal.fire({
                icon: 'error',
                title: 'Error al listar las ventas',
                text: msg
            });
        } finally {

        }


    }
    const nuevaVenta = {
        id: Date.now(),
        precio: 0,
        subtotal: 0,
        igv: 0,
        fecha: new Date().toLocaleDateString(),
        productos: []
    }
    ventas.value.push(nuevaVenta)
    ventaActiva.value = ventas.value.length - 1 // Activar la última venta agregada
}

const cerrarPanelPago = () => {
    ventaAPagar.value = null;
    procesarPago.value = false;
}

const eliminarVenta = (index) => {
    ventas.value.splice(index, 1)
    // Si eliminamos la venta activa, cambiamos la venta activa a la anterior
    if (index === ventaActiva.value && ventas.value.length > 0) {
        ventaActiva.value = Math.max(0, ventaActiva.value - 1) // Asegura que no quede fuera de rango
    } else if (ventas.value.length === 0) {
        ventaActiva.value = null // Si no quedan ventas, ponemos ventaActiva a null
    }
}
const verDetalle = (index) => {
    estaDetalleAbierto.value = true;
    const venta = ventas.value[index]
}
const navegarVenta = (direccion) => {
    if (direccion === 'adelante' && ventaActiva.value < ventas.value.length - 1) {
        ventaActiva.value++
    } else if (direccion === 'atras' && ventaActiva.value > 0) {
        ventaActiva.value--
    }
}
const agregarProducto = ({ producto, presentacion }) => {
    // Si no hay ventas, agregamos una nueva
    if (ventas.value.length === 0) {
        agregarVenta()
    }

    let venta = ventas.value[ventaActiva.value]

    // Si la venta activa ya está registrada, buscar otra no registrada
    if (venta.registrado) {
        // Buscar la primera venta no registrada
        const otraVenta = ventas.value.find(v => !v.registrado)
        if (otraVenta) {
            venta = otraVenta
            ventaActiva.value = ventas.value.indexOf(otraVenta)
        } else {
            // Si no hay ninguna, creamos una nueva y la usamos
            agregarVenta()
            venta = ventas.value[ventas.value.length - 1]
            ventaActiva.value = ventas.value.length - 1
        }
    }

    // Asegurarse de que tenga un array de productos
    if (!venta.productos) {
        venta.productos = []
    }

    // Creamos una clave única para detectar si ya está en el carrito
    const idProducto = presentacion ? `${producto.id}-${presentacion.id}` : `${producto.id}-unidad`
    const productoExistente = venta.productos.find(p => p.idUnico === idProducto)

    if (productoExistente) {
        productoExistente.cantidad += 1
    } else {

        const factor = presentacion?.factor ?? 1
        const unidad = producto.unidad
        const descripcion = presentacion ? `${producto.descripcion}-${presentacion.descripcion}` : `${producto.descripcion}`
        // Precio base con o sin presentación
        const monto_precio_unitario = presentacion ? presentacion.precio : producto.monto_venta
        const porcentaje_igv = parseFloat(producto.porcentaje_igv)
        const tipo_afectacion_igv = producto.tipo_afectacion_igv ?? '10' // '10' = gravado estándar

        let monto_valor_unitario = 0
        let monto_base_igv = 0
        let igv = 0
        let total_impuestos = 0
        let precio_sin_igv = 0
        let monto_valor_venta = 0

        // Cálculos de IGV y base imponible
        //corregir
        if (tipo_afectacion_igv === '10') {
            monto_valor_unitario = +(monto_precio_unitario / (1 + porcentaje_igv / 100)).toFixed(2)
            igv = +(monto_precio_unitario - monto_valor_unitario).toFixed(2)
            monto_base_igv = monto_valor_unitario
            total_impuestos = igv
            monto_valor_venta = monto_precio_unitario
            precio_sin_igv = monto_valor_unitario
        } else {
            monto_valor_unitario = monto_precio_unitario
            igv = 0
            monto_base_igv = 0
            total_impuestos = 0
            monto_valor_venta = monto_precio_unitario
            precio_sin_igv = monto_precio_unitario
        }

        venta.productos.push({
            idUnico: idProducto,
            producto_id: producto.id,
            descripcion,
            unidad,
            factor,
            categoria_producto: producto.categoria?.descripcion ?? null,
            cantidad: 1,

            monto_valor_unitario,
            monto_valor_gratuito: 0, // puedes agregar lógica si es necesario
            monto_valor_venta,
            monto_base_igv,
            monto_precio_unitario,
            porcentaje_igv,
            igv,
            tipo_afectacion_igv,
            total_impuestos,

            es_gratuita: false,
            es_icbper: false,
            icbper: 0,
            factor_icbper: 0,

            presentacion_id: presentacion?.id ?? null
        })
    }

    recalcularPrecio();
}

function quitarItem(producto) {
    const venta = ventas.value[ventaActiva.value];
    const index = venta.productos.findIndex(p => p.idUnico === producto.idUnico);

    if (index !== -1) {
        venta.productos.splice(index, 1);
        recalcularPrecio();
    }
}

function aumentarCantidad(producto) {
    const venta = ventas.value[ventaActiva.value];
    const item = venta.productos.find(p => p.idUnico === producto.idUnico);

    if (item) {
        item.cantidad += 1;
        recalcularPrecio();
    }
}

// Reducir cantidad

function reducirCantidad(producto) {
    const venta = ventas.value[ventaActiva.value];
    const item = venta.productos.find(p => p.idUnico === producto.idUnico);

    if (item) {
        if (item.cantidad > 1) {
            item.cantidad -= 1;
        } else {
            quitarItem(producto);
        }
        recalcularPrecio();
    }
}

function guardarPrecio({ id, nuevoPrecio }) {
    const venta = ventas.value[ventaActiva.value];
    const producto = venta.productos.find(p => p.idUnico === id);

    if (producto) {
        producto.monto_precio_unitario = parseFloat(nuevoPrecio);
        recalcularPrecio();
        cerrarModalPrecioUnitario();
    }
}

const actualizarClienteSeleccionado = (clienteData) => {
    cliente.value = clienteData;
}

const recalcularPrecio = () => {
    const venta = ventas.value[ventaActiva.value];

    // Totales por categoría SUNAT
    let gravadas = 0;
    let exoneradas = 0;
    let inafectas = 0;
    let exportacion = 0;
    let gratuitas = 0;

    let igv = 0;
    let igvGratuito = 0;
    let baseIvap = 0;
    let mtoIvap = 0;
    let icbperTotal = 0;

    const factorIcbper = 0.50;

    venta.productos.forEach(p => {
        const cantidad = parseFloat(p.cantidad || 0);
        const tipoAfectacion = p.tipo_afectacion_igv ?? '10';
        const porcentajeIgv = parseFloat(p.porcentaje_igv ?? 18);
        const precioUnitario = parseFloat(p.monto_precio_unitario || 0);

        let valorUnitario = 0;
        let igvUnitario = 0;

        if (tipoAfectacion === '10' || tipoAfectacion === '17') {
            valorUnitario = +(precioUnitario / (1 + porcentajeIgv / 100)).toFixed(6);
            igvUnitario = +(precioUnitario - valorUnitario).toFixed(6);
        } else {
            valorUnitario = precioUnitario;
            igvUnitario = 0;
        }

        const valorVenta = +(valorUnitario * cantidad).toFixed(2);
        const igvTotal = +(igvUnitario * cantidad).toFixed(2);
        const precioTotal = +(precioUnitario * cantidad).toFixed(2);

        let icbperItem = 0;
        if (p.codProducto === '9999' || p.descripcion?.toUpperCase().includes('BOLSA')) {
            icbperItem = +(cantidad * factorIcbper).toFixed(2);
            icbperTotal += icbperItem;
        }

        switch (tipoAfectacion) {
            case '10':
                gravadas += valorVenta;
                igv += igvTotal;
                break;
            case '11': case '12': case '13': case '14': case '15': case '16':
                gratuitas += valorVenta;
                igvGratuito += igvTotal;
                break;
            case '17':
                baseIvap += valorVenta;
                mtoIvap += igvTotal;
                break;
            case '20':
                exoneradas += valorVenta;
                break;
            case '21':
                gratuitas += valorVenta;
                break;
            case '30':
                inafectas += valorVenta;
                break;
            case '31': case '32': case '33': case '34': case '35': case '36':
                gratuitas += valorVenta;
                break;
            case '40':
                exportacion += valorVenta;
                break;
        }

        p.monto_valor_unitario = valorUnitario;
        p.igv = igvTotal;
        p.total_impuestos = +(igvTotal + icbperItem).toFixed(2);
        p.monto_valor_venta = valorVenta;
        p.monto_base_igv = valorVenta;
        p.icbper = icbperItem;
        p.subtotal = +(precioTotal + icbperItem).toFixed(2);
    });

    // Totales generales
    const valorVentaTotal = gravadas + exoneradas + inafectas + exportacion;
    const totalImpuestos = igv + igvGratuito + mtoIvap + icbperTotal;
    const subtotal = valorVentaTotal + totalImpuestos;

    // ✅ Redondeo a 2 decimales
    const totalVenta = +(subtotal.toFixed(2));
    const redondeo = +(totalVenta - subtotal).toFixed(2); // Puede ser positivo o negativo

    // Guardar en venta
    venta.monto_operaciones_gravadas = +gravadas.toFixed(2);
    venta.monto_operaciones_exoneradas = +exoneradas.toFixed(2);
    venta.monto_operaciones_inafectas = +inafectas.toFixed(2);
    venta.monto_operaciones_exportacion = +exportacion.toFixed(2);
    venta.monto_operaciones_gratuitas = +gratuitas.toFixed(2);
    venta.monto_igv = +igv.toFixed(2);
    venta.monto_igv_gratuito = +igvGratuito.toFixed(2);
    venta.monto_base_ivap = +baseIvap.toFixed(2);
    venta.monto_ivap = +mtoIvap.toFixed(2);
    venta.icbper = +icbperTotal.toFixed(2);
    venta.total_impuestos = +totalImpuestos.toFixed(2);
    venta.valor_venta = +valorVentaTotal.toFixed(2);
    venta.subtotal = +subtotal.toFixed(2);
    venta.monto_importe_venta = totalVenta;
    venta.redondeo = redondeo;
};


const procesarVenta = async () => {
    procesandoVenta.value = true;

    try {
        const metodoAgregados = metodosPagoAgregados.value.map(metodo => ({
            codigo: metodo.codigo,
            monto: metodo.amount
        }));
        const sucursalSeleccionada = localStorage.getItem('sucursalSeleccionada') ?? null;
        const payload = {
            metodos_pagos: metodoAgregados,
            cliente: cliente.value,
            venta: ventaAPagar.value,
            tipo_comprobante_codigo: tipoComprobante.value,
            sucursal_id: sucursalSeleccionada,
            fecha_emision: fechaEmision.value,
            totalPago: totalPago.value,
        };

        const { data } = await api.post('/venta/registrar', payload);

        if (data.success) {

            const ventaRegistrada = data.venta
            ventaRegistrada.registrado = true


            const index = ventas.value.findIndex(v => v.id === ventaAPagar.value.id)

            if (index !== -1) {
                // Reemplazar por la venta real del backend
                ventas.value[index] = {
                    ...ventaRegistrada
                }
            }
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message
            });
        }

    } catch (error) {
        const msg = error.response?.data?.message || 'Error desconocido';

        Swal.fire({
            icon: 'error',
            title: 'Error al procesar la venta',
            text: msg
        });
    } finally {
        procesandoVenta.value = false;
        cerrarPanelPago();
    }
};

agregarVenta();




</script>