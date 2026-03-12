<div x-data="{
    info: @entangle('infoAdicional'),
    clave: @entangle('nuevaClave'),
    valor: @entangle('nuevoValor'),
    ubicacion: @entangle('nuevaUbicacion'),

    agregar() {
        if (!this.clave || !this.valor) return

        this.info[this.ubicacion].push({
            id: null,
            clave: this.clave,
            valor: this.valor
        })

        this.clave = ''
        this.valor = ''
    },

    eliminar(seccion, index) {
        this.info[seccion].splice(index, 1)
    }
}" class="space-y-8">
    <!-- Formulario para agregar nueva información -->
    <div class="bg-muted text-muted-foreground p-4 rounded-lg">

        <flux:heading size="sm" class="mb-4">
            Agregar Información Adicional
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <flux:input x-model="clave" label="Nombre" placeholder="Ej: Celular, Cuenta BCP" />

            <flux:input x-model="valor" label="Valor" placeholder="Ej: 999999999" />

            <flux:select x-model="ubicacion" label="Ubicación">
                <option value="Cabecera">Cabecera</option>
                <option value="Centro">Información Adicional</option>
                <option value="Pie">Pie de Página</option>
            </flux:select>

        </div>

        <div class="mt-4">
            <flux:button @click="agregar" variant="primary" size="sm" icon="plus">
                Agregar
            </flux:button>
        </div>

    </div>



    <template x-for="(items,seccion) in info" :key="seccion">

        <div>

            <flux:heading size="sm" class="mb-4" x-text="seccion"></flux:heading>

            <x-table>
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="text-left">Nombre</x-th>
                        <x-th class="text-left">Valor</x-th>
                        <x-th class="text-center">Acciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    <template x-for="(item,index) in items" :key="index">

                        <x-tr>
                            <x-td x-text="item.clave"></x-td>

                            <x-td x-text="item.valor"></x-td>

                            <x-td class="text-center">

                                <flux:button @click="eliminar(seccion,index)" variant="danger" size="sm"
                                    icon="trash">
                                    Eliminar
                                </flux:button>

                            </x-td>
                        </x-tr>

                    </template>
                </x-slot>

            </x-table>

            <div x-show="items.length === 0" class="text-center p-4 bg-muted text-muted-foreground rounded-lg">
                No hay información
            </div>

        </div>

    </template>
</div>
