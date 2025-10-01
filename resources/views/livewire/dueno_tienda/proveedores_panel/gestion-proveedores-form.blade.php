<div>
    <flux:modal name="proveedores-form" class="md:w-4xl" wire:model="mostrarFormularioProveedores">

        <flux:heading size="lg" level="1">
            Proveedores
        </flux:heading>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mt-4 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="grid grid-cols-2 gap-4 mt-4">

            <flux:select label="Tipo Documento" wire:model="documento_tipo">
                <option value="">Seleccione</option>
                @foreach ($tipoDocumentos as $tipoDocumento)
                    <option value="{{ $tipoDocumento->codigo }}">{{ $tipoDocumento->nombre_corto }}</option>                    
                @endforeach
            </flux:select>

            <flux:input label="N° Documento" wire:model="documento_numero" />

            <flux:input label="Razón Social" wire:model="razon_social" />

            <flux:input label="Nombre Comercial" wire:model="nombre_comercial" />

            <flux:input label="Dirección" wire:model="direccion" />

            <flux:input label="Teléfono" wire:model="telefono" />

            <flux:input label="Correo electrónico" type="email" wire:model="email" />

            <flux:input label="Página Web" wire:model="pagina_web" />

            <flux:input label="Banco" wire:model="banco" />

            <flux:input label="Cuenta Bancaria" wire:model="cuenta_bancaria" />

            <flux:input label="CCI" wire:model="cci" />

            <flux:select label="Estado" wire:model="estado">
                <option value="ACTIVO">Activo</option>
                <option value="INACTIVO">Inactivo</option>
            </flux:select>

        </div>

        <x-flex class="justify-end mt-6 space-x-2">
            <flux:button variant="primary" icon="save" wire:click="storeProveedor">
                Guardar
            </flux:button>
        </x-flex>

    </flux:modal>

    <x-loading wire:loading />
</div>
