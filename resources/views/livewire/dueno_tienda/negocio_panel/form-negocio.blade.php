<flux:heading size="lg" class="mb-6">{{ $isEditing ? 'Editar Negocio' : 'Nuevo Negocio' }}</flux:heading>

<form wire:submit.prevent="save" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <flux:input wire:model="nombre_legal" label="Nombre Legal" placeholder="Nombre legal del negocio" required />
        <flux:input wire:model="nombre_comercial" label="Nombre Comercial" placeholder="Nombre comercial del negocio" required />
        <flux:input wire:model="ruc" label="RUC" placeholder="Número de RUC" maxlength="11" required />
        <flux:select wire:model="tipo_negocio" label="Tipo de Negocio" required>
            <option value="">Seleccione un tipo</option>
            @foreach ($tiposNegocio as $valor => $label)
                <option value="{{ $valor }}">{{ $label }}</option>
            @endforeach
        </flux:select>

        <flux:input wire:model="direccion" label="Dirección" placeholder="Dirección fiscal" required />
        <flux:input wire:model="ubigeo" label="Ubigeo" placeholder="Ej: 150101" />
        <flux:input wire:model="departamento" label="Departamento" placeholder="Ej: Lima" />
        <flux:input wire:model="provincia" label="Provincia" placeholder="Ej: Lima" />
        <flux:input wire:model="distrito" label="Distrito" placeholder="Ej: Lima" />
        <flux:input wire:model="codigo_pais" label="Código país" placeholder="Ej: PE" />
        <flux:input wire:model="urbanizacion" label="Urbanización" placeholder="Coloca la Urbanización" />
        

        <flux:input wire:model="usuario_sol" label="Usuario SOL" placeholder="Usuario SOL de SUNAT" required />
        <flux:input wire:model="clave_sol" label="Clave SOL" type="password" placeholder="Clave SOL de SUNAT"
            required />
        <flux:input wire:model="client_secret" label="Client Secret" placeholder="Client Secret (opcional)" />
        <flux:select wire:model="modo" label="Modo" required>
            <option value="desarrollo">Beta (Pruebas)</option>
            <option value="produccion">Producción</option>
        </flux:select>
        <div>
            <x-subir-file wire:model="certificado" label="Certificado Digital (.pem, .txt)" accept=".pem,.txt"
                :current-file="$certificado_actual" />
            @error('certificado')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <x-subir-file wire:model="logo_factura" label="Logo para Factura (.jpg, .png)" accept="image/jpeg,image/png"
                :current-file="$logo_actual" />
            @error('logo_factura')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>
</form>
