<template>
    <Card>
        <PanelProductos>
            <Title titulo="Panel de Ventas">
                <div class="md:flex items-center gap-3">
                    <Button @click="agregarVenta">
                        <i class="fa fa-plus"></i> Agregar venta
                    </Button>
                    <Button @click="$emit('mostrarModalSeleccionSucursal', true)">
                        <i class="fa fa-save"></i> Cambiar sucursal
                    </Button>
                </div>
            </Title>

            <PanelBuscarProducto @productoSeleccionado="agregarProducto" />

            <Button @click="testearCalculoVenta">
                <i class="fa fa-bug"></i> Probar Cálculo
            </Button>


        </PanelProductos>

        <PanelCarrito>
            <div v-if="ventas.length > 0" class="flex flex-col h-full">
                <Spacing>
                    <PanelSeleccionadorCliente :clienteSeleccionado="cliente"
                        @clienteSeleccionado="actualizarClienteSeleccionado" />
                    <Flex class="justify-between my-3">
                        <Button @click="navegarVenta('atras')" :disabled="ventaActiva === 0">
                            <i class="fa fa-chevron-left"></i>
                        </Button>

                        <span>Venta n° {{ ventaActiva + 1 }}</span>

                        <Button @click="navegarVenta('adelante')" :disabled="ventaActiva === ventas.length - 1">
                            <i class="fa fa-chevron-right"></i>
                        </Button>
                    </Flex>
                </Spacing>

                <!-- Parte navegable y scrollable -->

                <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-4">

                    <ListaProductos :productos="ventas[ventaActiva].productos" @quitarCarrito="quitarItem"
                        @agregarCantidad="aumentarCantidad" @quitarCantidad="reducirCantidad"
                        @editarPrecioUnitario="productoSeleccionado = $event" />

                </div>

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
                                            {{ ventas[ventaActiva].subtotal }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            IGV
                                        </td>
                                        <td class="text-right">
                                            {{ ventas[ventaActiva].igv }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-left">
                                            Total
                                        </th>
                                        <th class="text-right">
                                            {{ ventas[ventaActiva].precio }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </Flex>

                        <Flex class="justify-between mt-4">
                            <Button @click="pagar">
                                <i class="fa fa-check"></i> Pagar
                            </Button>

                            <Button @click="eliminarVenta(ventaActiva)" variant="danger">
                                <i class="fa fa-trash"></i> Eliminar Venta
                            </Button>
                        </Flex>
                    </Spacing>


                </div>
            </div>


        </PanelCarrito>

        <!-- Mostrar modal solo si hay un producto seleccionado -->
        <ModalCambiarPrecioUnitario v-if="productoSeleccionado" :producto="productoSeleccionado"
            @cerrarModal="productoSeleccionado = null" @guardarPrecio="guardarPrecio" />

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
                            Total: S/. <span class="subtotal">{{ ventaAPagar.precio }}</span>
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
                        <div class="flex items-center">
                            <input id="default-radio-3" type="radio" value="" class="w-4 h-4"
                                v-model="tipoComprobante" />
                            <Label for="default-radio-3">Ticket</Label>
                        </div>
                    </Spacing>
                    <div class="flex items-center">

                        <button @click="procesarVenta"
                            class="w-1/2 bg-primary hover:opacity-90 transition cursor-pointer border-0 hover:bg-primaryHoverOpacity text-white focus:outline-none text-2xl flex items-center justify-center">
                            <Spacing>
                                <i class="fa fa-check mr-2"></i> Procesar Venta
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
import ListaProductos from '@/components/ListaProductos.vue'
import ModalCambiarPrecioUnitario from '@/components/ModalCambiarPrecioUnitario.vue'
import PanelSeleccionadorCliente from '@/components/PanelSeleccionadorCliente.vue'
import Spacing from '@/components/ui/Spacing.vue'

const ventas = ref([])
const ventaActiva = ref(null)
const productoSeleccionado = ref(null)
const cliente = ref(null)
const procesarPago = ref(false)
const ventaAPagar = ref(null)
const fechaEmision = ref(null)
const tipoComprobante = ref('factura')
const metodosPago = ref({
    cash: { icon: 'fa-money-bill', label: 'Efectivo', amount: '0' },
    card: { icon: 'fa-credit-card', label: 'Tarjeta', amount: '0' },
    yape: { icon: 'fa-mobile-alt', label: 'Yape', amount: '0' },
    plin: { icon: 'fa-qrcode', label: 'Plin', amount: '0' }
})
// Métodos de pago agregados
const metodosPagoAgregados = ref([]);

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
            metodoAgregado.amount = ventaAPagar.value.precio;

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
    // Agregar el pago de 'Efectivo' con el valor de ventaAPagar.precio
    agregarPago('cash');

    // Otros métodos de pago pueden ser agregados según lo que necesites después
};

const agregarVenta = () => {
    const nuevaVenta = {
        id: Date.now(),
        precio: 0,
        subtotal: 0,
        igv: 0,
        fecha: new Date().toLocaleDateString(),
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

const navegarVenta = (direccion) => {
    if (direccion === 'adelante' && ventaActiva.value < ventas.value.length - 1) {
        ventaActiva.value++
    } else if (direccion === 'atras' && ventaActiva.value > 0) {
        ventaActiva.value--
    }
}
const agregarProducto = ({ producto, presentacion }) => {
    // Si no hay ninguna venta activa, agregamos una nueva venta
    if (ventas.value.length === 0) {
        agregarVenta()
    }

    const venta = ventas.value[ventaActiva.value]

    // Si la venta no tiene productos aún, los inicializamos
    if (!venta.productos) {
        venta.productos = []
    }

    // Creamos una clave única para detectar si ya está en el carrito
    const idProducto = presentacion ? `${producto.id}-${presentacion.id}` : `${producto.id}-unidad`


    const productoExistente = venta.productos.find(p => p.idUnico === idProducto)

    if (productoExistente) {
        productoExistente.cantidad += 1
    } else {
        venta.productos.push({
            idUnico: idProducto,
            producto_id: producto.id,
            nombre: producto.nombre_producto,
            precio: presentacion ? presentacion.precio : producto.precio_base,
            descripcion: presentacion ? presentacion.descripcion : 'Unidad',
            factor: presentacion ? presentacion.factor : 1,
            categoria_producto: producto.categoria?.descripcion,
            cantidad: 1,
            igv: producto.igv,
            presentacion_id: presentacion?.id ?? null,
            tipo_afectacion_igv:producto.tipo_afectacion_igv,
            precio_sin_igv: 0,
            monto_igv: 0,
            subtotal: 0,
            total_impuestos: 0,
        })
    }

    recalcularPrecio();
}
// Quitar producto del carrito
function quitarItem(producto) {
    const index = ventas.value[ventaActiva.value].productos.findIndex(p =>
        p.idUnico === producto.idUnico
    )

    if (index !== -1) {
        ventas.value[ventaActiva.value].productos.splice(index, 1);
        recalcularPrecio();
    }
}


// Aumentar cantidad
function aumentarCantidad(producto) {
    const item = ventas.value[ventaActiva.value].productos.find(p =>
        p.idUnico === producto.idUnico
    )
    if (item) {
        item.cantidad += 1;
        recalcularPrecio();
    }
}

// Reducir cantidad
function reducirCantidad(producto) {
    console.log(producto);
    console.log(ventas.value[ventaActiva.value].productos);

    const item = ventas.value[ventaActiva.value].productos.find(p =>
        p.idUnico === producto.idUnico
    )
    if (item && item.cantidad > 1) {
        item.cantidad -= 1
    } else {
        quitarItem(producto)
    }
    recalcularPrecio();
}

// Cuando se edita el precio de un producto
function guardarPrecio({ id, nuevoPrecio }) {
    const venta = ventas.value[ventaActiva.value]
    const producto = venta.productos.find(prod => prod.idUnico === id)
    if (producto) {
        producto.precio = nuevoPrecio
        recalcularPrecio();
    }
}

const actualizarClienteSeleccionado = (clienteData) => {
    cliente.value = clienteData;
}

const recalcularPrecio = () => {
    const venta = ventas.value[ventaActiva.value];

    let totalSinIgv = 0;
    let totalIgv = 0;

    venta.productos.forEach(p => {
        const igv = p.igv || 0;
        const precioConIgv = p.precio; // incluye IGV

        if (igv === 0) {
            // Producto inafecto o exonerado
            p.precio_sin_igv = precioConIgv;
            p.monto_igv = 0;
        } else {
            // Se calcula base imponible y monto IGV a partir del precio con IGV
            const precioSinIgvUnitario = +(precioConIgv / (1 + igv / 100)).toFixed(2);
            const montoIgvUnitario = +(precioConIgv - precioSinIgvUnitario).toFixed(2);

            p.precio_sin_igv = precioSinIgvUnitario;
            p.monto_igv = montoIgvUnitario;
        }

        // Subtotales por ítem
        p.subtotal = +(p.precio * p.cantidad).toFixed(2); // precio incluye IGV

        // Acumulamos totales
        const igvTotal = +(p.monto_igv * p.cantidad).toFixed(2);
        totalSinIgv += +(p.precio_sin_igv * p.cantidad).toFixed(2);
        totalIgv += igvTotal;
        p.total_impuestos = igvTotal;
    });

    // Totales para la venta
    venta.subtotal = totalSinIgv.toFixed(2); // base imponible
    venta.igv = totalIgv.toFixed(2);
    venta.precio = (totalSinIgv + totalIgv).toFixed(2); // total con IGV
};

const procesarVenta = async () => {
    if (ventaAPagar.value.precio <= 0) {
        alert("La venta no tiene items.");
        return;
    }
    if (totalPago.value != ventaAPagar.value.precio) {
        alert("El monto a pagar no coincide con el total de la venta, " + totalPago.value + " no es igual a " + ventaAPagar.value.precio + ".");
        return;
    }
    if (tipoComprobante.value == 'factura' && cliente.value == null) {
        alert("Necesita agregar un cliente para la factura.");
        return;
    }
    if (tipoComprobante.value == 'boleta' && ventaAPagar.value.precio > 700 && cliente.value == null) {
        alert("Para boletas con monto mayor a S/ 700 soles. debe agregar un cliente.");
        return;
    }

    if (fechaEmision.value == null) {
        alert("Debe ingresar una fecha de emisión válida.");
        return;
    }


    try {
        console.log(ventaAPagar.value, cliente.value, tipoComprobante.value, fechaEmision.value);

        /*
        ventaAPagar = {
            id: 1744303230192,
            fecha: "10/4/2025",
            subtotal: "632.21",
            igv: "113.79",
            precio: "746.00",
            productos: [
                {
                    producto_id: 1,
                    nombre: "Fierrro de 2media calidad A1 WASH",
                    descripcion: "Unidad",
                    cantidad: 2,
                    precio: "123.00",
                    precio_sin_igv: 104.24,
                    igv: "18.00",
                    monto_igv: 18.76,
                    subtotal: 246,
                    presentacion_id: null,
                    idUnico: "1-unidad"
                },
                {
                    producto_id: 1,
                    nombre: "Fierrro de 2media calidad A1 WASH",
                    descripcion: "Caja x6",
                    cantidad: 1,
                    precio: "500.00",
                    precio_sin_igv: 423.73,
                    igv: "18.00",
                    monto_igv: 76.27,
                    subtotal: 500,
                    presentacion_id: 1,
                    idUnico: "1-1"
                }
            ]
        }

        cliente = {
            id: 3,
            nombre_completo: "asd ajsdjjasjdjasjasd",
            numero_documento: "23423423423",
            tipo_documento_id: "7",
            tipo_cliente_id: "persona",
            direccion: null,
            direccion_facturacion: null,
            departamento: null,
            provincia: null,
            distrito: null,
            telefono: null,
            email: null,
            ruc_facturacion: null,
            puntos: 0,
            notas: null,
            dueno_tienda_id: 2,
            created_at: "2025-04-09T09:47:23.000000Z",
            updated_at: "2025-04-09T09:47:23.000000Z"
        }

        tipoComprobante = "factura"
        fechaEmision = "2025-04-09"
        const sucursalSeleccionada = ref(localStorage.getItem('sucursalSeleccionada'))
        */


        const metodoAgregados = metodosPagoAgregados.value.map(metodo => ({
            codigo: metodo.codigo,
            monto: metodo.amount
        }));

        const sucursalSeleccionada = localStorage.getItem('sucursalSeleccionada') ?? null;
        const nombreCliente = cliente.value?.nombre_completo ?? cliente.value?.nombre_comercial;
        const payload = {
            metodos_pagos: metodoAgregados,
            cliente_id: cliente.value?.id ?? null,
            nombre_cliente: nombreCliente ?? null,
            documento_cliente: cliente.value?.numero_documento ?? null,
            tipo_documento_cliente: cliente.value?.tipo_documento_id ?? null,

            subtotal: parseFloat(ventaAPagar.value.subtotal),
            igv: parseFloat(ventaAPagar.value.igv),
            total: parseFloat(ventaAPagar.value.precio),
            total_pagado: parseFloat(ventaAPagar.value.precio),

            tipo_comprobante_codigo: tipoComprobante.value,
            serie_comprobante: null,
            correlativo_comprobante: null,

            caja_id: null,
            sucursal_id: sucursalSeleccionada,

            fecha_emision: fechaEmision.value,
            fecha_pago: null,

            detalles: ventaAPagar.value.productos.map(p => ({
                producto_id: p.producto_id ?? null,
                nombre_producto: p.nombre,
                unidad: p.descripcion,
                categoria_producto: p.categoria_producto ?? null,
                factor: p.factor,
                precio_unitario: parseFloat(p.precio),
                cantidad: p.cantidad,
                subtotal: p.subtotal - p.total_impuestos,
                porcentaje_igv: parseFloat(p.igv),
                total_impuestos: p.total_impuestos,
                igv: p.monto_igv ?? 0,
                total: p.subtotal,
                tipo_afectacion_igv:p.tipo_afectacion_igv
            }))
        };

        const { data } = await api.post('/venta/registrar', payload); // <- MEJOR USAR POST
        console.log(data);

    } catch (error) {
        console.log(error)
        //resultados.value = []
    } finally {
        //cargando.value = false
    }


}
agregarVenta();

const testearCalculoVenta = () => {
    const productosPrueba = [
        { id: 1, nombre: 'Producto A', precio: 11.87, cantidad: 7, igv: 18 },
        { id: 2, nombre: 'Producto B', precio: 23.63, cantidad: 3, igv: 18 },
        { id: 3, nombre: 'Producto C', precio: 10.04, cantidad: 5, igv: 18 },
        { id: 4, nombre: 'Producto D', precio: 59.01, cantidad: 2, igv: 18 },
        { id: 5, nombre: 'Producto E', precio: 25.15, cantidad: 6, igv: 18 },
        { id: 6, nombre: 'Producto F', precio: 18.88, cantidad: 9, igv: 18 },
        { id: 7, nombre: 'Producto G', precio: 12.00, cantidad: 4, igv: 0 },
        { id: 8, nombre: 'Producto H', precio: 100.00, cantidad: 1, igv: 18 },
        { id: 9, nombre: 'Producto I', precio: 8.49, cantidad: 8, igv: 0 },
        { id: 10, nombre: 'Producto J', precio: 37.76, cantidad: 2, igv: 18 },
        { id: 11, nombre: 'Producto K', precio: 19.97, cantidad: 3, igv: 18 },
        { id: 12, nombre: 'Producto L', precio: 7.77, cantidad: 11, igv: 0 },
        { id: 13, nombre: 'Producto M', precio: 14.88, cantidad: 5, igv: 18 },
        { id: 14, nombre: 'Producto N', precio: 2.55, cantidad: 13, igv: 18 },
        { id: 15, nombre: 'Producto O', precio: 9.91, cantidad: 7, igv: 0 },
        { id: 16, nombre: 'Producto P', precio: 63.39, cantidad: 2, igv: 18 },
        { id: 17, nombre: 'Producto Q', precio: 88.88, cantidad: 1, igv: 18 },
        { id: 18, nombre: 'Producto R', precio: 1.99, cantidad: 14, igv: 0 },
        { id: 19, nombre: 'Producto S', precio: 3.14, cantidad: 10, igv: 18 },
        { id: 20, nombre: 'Producto T', precio: 45.71, cantidad: 2, igv: 0 },
    ];


    agregarVenta();

    const venta = ventas.value[ventaActiva.value];
    venta.productos = productosPrueba.map((p, index) => ({
        idUnico: `test-${index}`,
        producto_id: p.id,
        nombre: p.nombre,
        precio: p.precio,
        cantidad: p.cantidad,
        igv: p.igv,
        descripcion: 'Prueba',
        presentacion_id: null,
    }));

    recalcularPrecio();

    // Validación
    //const totalIgvSumado = venta.productos.reduce((acc, p) => acc + +(p.monto_igv * p.cantidad).toFixed(2), 0).toFixed(2);
    const totalIgvSumado = venta.productos.reduce((acc, p) => acc + +p.total_impuestos, 0).toFixed(2);

    const subtotalSumado = venta.productos.reduce((acc, p) => acc + +(p.precio_sin_igv * p.cantidad).toFixed(2), 0).toFixed(2);
    //const totalSumado = (parseFloat(totalIgvSumado) + parseFloat(subtotalSumado)).toFixed(2);

    // Validación usando el total_impuestos ya calculado
    const totalSumado = (parseFloat(totalIgvSumado) + parseFloat(subtotalSumado)).toFixed(2);


    console.log('Subtotal esperado:', venta.subtotal, '==', subtotalSumado);
    console.log('IGV esperado:', venta.igv, '==', totalIgvSumado);
    console.log('Total esperado:', venta.precio, '==', totalSumado);

    if (venta.subtotal === subtotalSumado && venta.igv === totalIgvSumado && venta.precio === totalSumado) {
        console.log('%c✅ TEST PASADO: Los montos coinciden.', 'color: green; font-weight: bold;');
    } else {
        console.error('❌ TEST FALLIDO: Hay inconsistencias en el cálculo.');
    }
};



</script>