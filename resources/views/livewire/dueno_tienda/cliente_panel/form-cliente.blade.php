<form wire:submit.prevent="guardarCliente">
    <div x-data="{ expandido: false }" class="space-y-4">

        <!-- Información básica -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <flux:select wire:model="tipo_cliente_id" label="Tipo de Cliente">
                <option value="">Seleccione un tipo</option>
                <option value="persona">Persona</option>
                <option value="empresa">Empresa</option>
            </flux:select>

            <flux:select wire:model="tipo_documento_id" label="Tipo de Documento">
                <option value="">Seleccione un tipo de documento</option>
                <option value="1">DNI</option>
                <option value="6">RUC</option>
                <option value="4">Carnet de Extranjería</option>
                <option value="7">Pasaporte</option>
            </flux:select>
            <template x-if="$wire.tipo_documento_id === '6'">
                <flux:field>
                    <flux:label>N° Documento / RUC</flux:label>
                    <flux:input.group>
                        <flux:input wire:model="numero_documento" type="text" />
                        <flux:button type="button" icon="magnifying-glass" wire:click="sunat">
                            Buscar en la SUNAT
                        </flux:button>
                    </flux:input.group>
                </flux:field>


            </template>
            <template x-if="$wire.tipo_documento_id !== '6'">
                <flux:input wire:model="numero_documento" label="N° Documento / RUC" type="text" />
            </template>

            <flux:input wire:model="nombre_completo" label="Nombre Completo /  Razón Social" type="text" />

            <template x-if="$wire.tipo_cliente_id === 'empresa'">
                <flux:input wire:model="nombre_comercial" label="Nombre Comercial" type="text" />
            </template>

            <flux:input wire:model="direccion" label="Dirección" type="text" />
            <flux:input wire:model="distrito" label="Distrito" type="text" />
            <flux:input wire:model="provincia" label="Provincia" type="text" />
            <flux:input wire:model="departamento" label="Departamento" type="text" />

            
        </div>

        <!-- Botón expandir ubicación y factura -->
        <flux:button variant="filled" class="mt-4" @click="expandido = !expandido" type="button">
            <span x-show="!expandido">Mostrar más opciones</span>
            <span x-show="expandido">Mostrar menos opciones</span>
        </flux:button>

        <!-- Información adicional -->
        <div x-show="expandido" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input wire:model="telefono" label="Teléfono" type="text" />
            <flux:input wire:model="whatsapp" label="WhatsApp" type="text" />
            <flux:input wire:model="email" label="Correo Electrónico" type="email" />
            <flux:textarea wire:model="notas" label="Notas internas (opcional)" rows="3" />

            <flux:input wire:model="puntos" label="Puntos Acumulados" type="number" />
        </div>
    </div>
    <div class="mt-6 flex justify-end">
        <flux:button type="button" @click="$wire.set('mostrarFormulario',false)" class="mr-2 pointer">
            Cancelar
        </flux:button>
        <flux:button variant="primary" type="submit">
            {{ $clienteId ? 'Actualizar' : 'Guardar' }}
        </flux:button>
    </div>
</form>
