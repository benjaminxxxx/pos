<div x-data="gestionRegistroCompra" x-init="$watch('productosAgregados', () => calcularTotales())">
    <x-flex class="items-start">
        <x-card class="w-full lg:w-[30rem]">
            <div class="bg-amber-500 text-white p-3 rounded text-center">
                <h2 class="text-lg font-semibold">TOTAL S/. <span x-text="total.toFixed(2)"></span></h2>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-1 md:col-span-2">
                    <flux:select wire:model="proveedorSeleccionado" label="Proveedor"
                        placeholder="Seleccione un proveedor">
                        @foreach ($proveedores as $proveedor)
                            <flux:select.option value="{{ $proveedor->id }}">
                                {{ $proveedor->razon_social . ' / ' . $proveedor->nombre_comercial }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                
                <div class="col-span-1 md:col-span-2 grid grid-cols-2 gap-4">
                    <!-- Nuevos campos: Moneda y Tipo de Cambio -->
                    <div>
                        <flux:select wire:model.live="moneda" label="Moneda" placeholder="Moneda">
                            <flux:select.option value="PEN">Soles (PEN)</flux:select.option>
                            <flux:select.option value="USD">Dólares (USD)</flux:select.option>
                        </flux:select>
                    </div>
                    <div>
                        <flux:input icon="arrow-path" type="number" step="0.0001" wire:model.live="tipoCambio"
                            label="T.C. (Si es USD)" placeholder="Tasa de cambio" 
                            :disabled="$moneda == 'PEN'" />
                    </div>
                </div>

                <div>
                    <flux:select wire:model.live="tipoComprobante" label="Tipo de Comprobante"
                        placeholder="Seleccione un tipo de comprobante">
                        <flux:select.option value="BOLETA">Boleta</flux:select.option>
                        <flux:select.option value="FACTURA">Factura</flux:select.option>
                        <flux:select.option value="TICKET">Ticket</flux:select.option>
                    </flux:select>
                </div>
                <div>
                    <flux:input icon="clipboard-document-check" wire:model="numeroComprobante" label="N° Comprobante"
                        placeholder="Número de comprobante" />
                </div>
                <div>
                    <flux:input icon="calendar" type="date" wire:model="fechaComprobante"
                        label="Fecha Comprobante" />
                </div>
                <!-- Nuevo campo: Fecha de Vencimiento -->
                <div>
                    <flux:input icon="calendar-days" type="date" wire:model="fechaVencimiento"
                        label="Fecha Vencimiento" />
                </div>
                <div>
                    <flux:select wire:model="formaPago" label="Forma de Pago" placeholder="Forma de Pago">
                        <flux:select.option value="CONTADO">Contado</flux:select.option>
                        <flux:select.option value="CREDITO">Crédito</flux:select.option>
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model="sucursalSeleccionada" label="Sucursal"
                        placeholder="Seleccionar una sucursal">
                        @foreach ($sucursales as $sucursal)
                            <flux:select.option value="{{ $sucursal->id }}">
                                {{ $sucursal->nombre }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                
                <!-- Nuevo campo: Glosa/Observación -->
                <div class="md:col-span-2">
                    <flux:textarea wire:model="glosaObservacion" label="Glosa / Observación"
                        placeholder="Detalles adicionales de la compra" />
                </div>

                <div class="md:col-span-2">
                    <x-button wire:click="guardarCompra" class="w-full mt-3" 
                        :disabled="count($productosAgregados) === 0 || !$sucursalSeleccionada">
                        <i class="fa fa-save"></i> Registrar Compra
                    </x-button>
                </div>
            </div>
        </x-card>
        <x-card class="flex-1">
            <div class="relative">
                <flux:input icon="magnifying-glass" x-model="buscar" @input.debounce.300ms="filtrarProductos" label="Buscar producto"
                    placeholder="Ingrese el nombre o código" />

                <!-- Resultados flotantes -->
                <div x-show="filtrados.length" x-transition.opacity
                    class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                    <template x-for="producto in filtrados" :key="producto.id">
                        <div @click="seleccionar(producto)"
                            class="cursor-pointer px-4 py-2 hover:bg-blue-50 transition-colors">
                            <div class="font-medium text-gray-800" x-text="producto.descripcion"></div>
                            <div class="text-sm text-gray-500 flex justify-between">
                                <span x-text="producto.codigo"></span>
                                <span class="font-semibold text-gray-700">Stock: <span
                                        x-text="producto.stock_actual"></span></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Sin resultados -->
                <div x-show="buscar && !filtrados.length" x-transition.opacity
                    class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg p-3 text-gray-500 italic">
                    No se encontraron productos que coincidan.
                </div>
            </div>
            
            <!-- Tabla de productos agregados -->
            <div class="mt-6 overflow-x-auto">
                <x-table>
                    <x-slot name="thead">
                        <x-tr>
                            <x-th class="text-left">Producto</x-th>
                            <x-th class="text-center">Cant.</x-th>
                            <x-th class="text-center">Costo Unitario</x-th>
                            <x-th class="text-center">Desc. (%)</x-th>
                            <x-th class="text-center">IGV (%)</x-th>
                            <x-th class="text-center">Total Línea</x-th>
                            <x-th class="text-center">Quitar</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        <template x-for="(item, i) in productosAgregados" :key="item.id">
                            <x-tr>
                                <x-td>
                                    <div class="font-semibold" x-text="item.descripcion"></div>
                                    <div class="text-xs text-gray-500" x-text="'Unidad: ' + item.unidad_medida"></div>
                                </x-td>

                                <x-td class="text-center">
                                    <input type="number" min="0.0001" step="0.0001" x-model.number="item.cantidad"
                                        class="w-20 text-center border rounded p-1" @input="calcularTotales()" />
                                </x-td>

                                <!-- Renombrado de Precio a Costo Unitario -->
                                <x-td class="text-center">
                                    <input type="number" min="0" step="0.0001" x-model.number="item.costo_unitario"
                                        class="w-24 text-center border rounded p-1" @input="calcularTotales()" />
                                </x-td>
                                
                                <!-- Descuento Porcentaje -->
                                <x-td class="text-center">
                                    <input type="number" min="0" max="100" step="1" x-model.number="item.descuento_porcentaje"
                                        class="w-16 text-center border rounded p-1" @input="calcularTotales()" />
                                </x-td>

                                <!-- Porcentaje IGV -->
                                <x-td class="text-center">
                                    <select x-model.number="item.porcentaje_igv" class="border rounded text-sm p-1" @change="calcularTotales()">
                                        <option value="0">0%</option>
                                        <option value="10">10%</option>
                                        <option value="18">18%</option>
                                    </select>
                                </x-td>

                                <x-td class="text-center font-semibold">
                                    <span x-text="calcularTotalLinea(item).toFixed(2)"></span>
                                </x-td>

                                <x-td class="text-center">
                                    <button @click="productosAgregados.splice(i,1); calcularTotales();"
                                        class="text-red-500 hover:text-red-700">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </x-td>
                            </x-tr>
                        </template>
                        <x-tr x-show="productosAgregados.length === 0">
                            <x-td colspan="7" class="text-center italic text-gray-500">Agregue productos a la compra usando el buscador.</x-td>
                        </x-tr>
                    </x-slot>
                </x-table>
            </div>

            <!-- Totales -->
            <div class="mt-6 text-right space-y-1 text-base p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl dark:bg-gray-700 dark:border-gray-600">
                <div><span class="font-semibold">Subtotal Neto:</span> S/. <span x-text="subtotal.toFixed(2)"></span></div>
                <div><span class="font-semibold">IGV (Impuesto):</span> S/. <span x-text="igv.toFixed(2)"></span></div>
                <div class="font-bold text-xl text-amber-600">TOTAL: S/. <span x-text="total.toFixed(2)"></span></div>
            </div>
        </x-card>
    </x-flex>
</div>
@script
<script>
    Alpine.data('gestionRegistroCompra', () => ({
        buscar: '',
        tipoComprobante: @entangle('tipoComprobante').defer,
        productos: @entangle('productos'), // Lista de todos los productos disponibles
        productosAgregados: @entangle('productosAgregados').live, // Detalle de la compra
        subtotal: @entangle('subtotal').live,
        igv: @entangle('igv').live,
        total: @entangle('total').live,

        // Inicializa los totales cuando el componente carga
        init() {
            this.calcularTotales();
        },

        get filtrados() {
            if (!this.buscar || this.buscar.length < 3) return []; // Solo buscar con 3+ caracteres
            const term = this.buscar.toLowerCase();
            return this.productos.filter(p =>
                (p.descripcion && p.descripcion.toLowerCase().includes(term)) ||
                (p.codigo && p.codigo.toLowerCase().includes(term))
            );
        },

        seleccionar(producto) {
            const existe = this.productosAgregados.find(p => p.id === producto.id);
            
            if (!existe) {
                this.productosAgregados.push({
                    id: producto.id,
                    producto_id: producto.producto_id,
                    descripcion: producto.descripcion,
                    codigo: producto.codigo,
                    // Campos del detalle de compra (con defaults para el servicio)
                    cantidad: 1,
                    costo_unitario: parseFloat(producto.costo_unitario || 0) || 0, // Usar costo_unitario real del producto
                    unidad_medida: producto.unidad_medida || 'UNIDAD', // Usar la unidad del producto
                    factor_conversion: producto.factor || 1, 
                    descuento_porcentaje: 0, 
                    tipo_igv: 'GRAVADO', // Default
                    porcentaje_igv: 18, // Default
                });
                this.calcularTotales();
            } else {
                existe.cantidad++;
                this.calcularTotales();
            }
            this.buscar = '';
        },

         calcularTotalLinea(item) {
            const cantidad = parseFloat(item.cantidad || 0);
            const costoUnitarioTotal = parseFloat(item.costo_unitario || 0); // Precio FINAL ingresado
            const descPorcentaje = parseFloat(item.descuento_porcentaje || 0) / 100;
            const igvPorcentaje = parseFloat(item.porcentaje_igv || 0) / 100;

            // 1. Calcular la Base Imponible Unitario a partir del Costo Unitario Total
            const factor = 1 + igvPorcentaje;
            // Si hay IGV, dividimos para obtener el precio sin IGV (Base Imponible)
            const baseImponibleUnitario = igvPorcentaje > 0 ? (costoUnitarioTotal / factor) : costoUnitarioTotal; 

            // 2. Subtotal Bruto (Base Imponible sin descuento)
            const subtotalBruto = baseImponibleUnitario * cantidad;
            
            // 3. Descuento aplicado a la Base Imponible
            const descuentoMonto = subtotalBruto * descPorcentaje;
            
            // 4. Subtotal Neto (Base Imponible con descuento)
            const subtotalNeto = subtotalBruto - descuentoMonto;
            
            // 5. IGV
            const igvMonto = subtotalNeto * igvPorcentaje;

            // 6. Total Línea (Total que debe pagar)
            return subtotalNeto + igvMonto;
        },

        calcularTotales() {
            let baseImponibleTotal = 0;
            let igvTotalAcumulado = 0;
            let montoTotalPagar = 0;

            this.productosAgregados.forEach(item => {
                const cantidad = parseFloat(item.cantidad || 0);
                const costoUnitarioTotal = parseFloat(item.costo_unitario || 0);
                const descPorcentaje = parseFloat(item.descuento_porcentaje || 0) / 100;
                const igvPorcentaje = parseFloat(item.porcentaje_igv || 0) / 100;
                
                // 1. Calcular la Base Imponible Unitario
                const factor = 1 + igvPorcentaje;
                // Si hay IGV, dividimos para obtener el precio sin IGV (Base Imponible)
                const baseImponibleUnitario = igvPorcentaje > 0 ? (costoUnitarioTotal / factor) : costoUnitarioTotal;
                
                // 2. Subtotal Bruto
                const subtotalBruto = baseImponibleUnitario * cantidad;
                
                // 3. Descuento
                const descuentoMonto = subtotalBruto * descPorcentaje;
                
                // 4. Subtotal Neto (Base Imponible)
                const neto = subtotalBruto - descuentoMonto;
                
                // 5. IGV
                const igvMonto = neto * igvPorcentaje;
                
                // 6. Total de la línea
                const totalLinea = neto + igvMonto;

                baseImponibleTotal += neto;
                igvTotalAcumulado += igvMonto;
                montoTotalPagar += totalLinea;
            });

            this.subtotal = parseFloat(baseImponibleTotal.toFixed(2));
            this.igv = parseFloat(igvTotalAcumulado.toFixed(2));
            this.total = parseFloat(montoTotalPagar.toFixed(2));
        
            // Emitir evento a Livewire para actualizar sus propiedades
            this.$wire.set('subtotal', this.subtotal);
            this.$wire.set('igv', this.igv);
            this.$wire.set('total', this.total);
        },
    }));
</script>
@endscript
