<form wire:submit.prevent="guardarNegocio" class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <flux:input wire:model="nombre_legal" label="Nombre Legal" placeholder="Nombre legal del negocio" required />

        <flux:input wire:model="nombre_comercial" label="Nombre Comercial" placeholder="Nombre comercial" required />

        <flux:input wire:model="ruc" label="RUC" maxlength="11" required />

        <flux:select wire:model="tipo_negocio" label="Tipo de Negocio" required>
            <option value="">Seleccione</option>
            @foreach ($tiposNegocio as $valor => $label)
                <option value="{{ $valor }}">{{ $label }}</option>
            @endforeach
        </flux:select>

        <flux:input wire:model="direccion" label="Dirección" required />

        <flux:input wire:model="codigo_pais" label="Código País" placeholder="PE" required />

        <flux:input wire:model="usuario_sol" label="Usuario SOL" required />

        <flux:input wire:model="clave_sol" type="password" label="Clave SOL" required />

        <flux:select wire:model="modo" label="Modo" required>
            <option value="desarrollo">Beta (Pruebas)</option>
            <option value="produccion">Producción</option>
        </flux:select>

    </div>

</form>
