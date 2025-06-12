<flux:heading size="lg" class="mb-6">{{ $isEditing ? 'Editar Negocio' : 'Nuevo Negocio' }}</flux:heading>

<form wire:submit.prevent="save" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <flux:input wire:model="nombre_legal" label="Nombre Legal" placeholder="Nombre legal del negocio" required />
        <flux:input wire:model="nombre_comercial" label="Nombre Comercial" placeholder="Nombre comercial del negocio"
            required />
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
            {{-- Vista previa cuando hay imagen temporal --}}
            @if ($certificado)
                <img src="{{ asset('image/pemfile.png') }}" alt="Certificado Pem" width="100" height="100"
                    class="mb-2 max-w-xs max-h-[10rem] rounded-lg shadow-lg">
                <flux:button wire:click="eliminarImagenCertificado" variant="danger" class="mt-2">Eliminar Imagen
                </flux:button>

                {{-- Mostrar imagen ya guardada si no hay temporal --}}
            @elseif ($certificado_actual)
                <img src="{{ asset('image/pemfile.png') }}" alt="Certificado Pem" width="100" height="100"
                    class="mb-2 max-w-xs max-h-[10rem] rounded-lg shadow-lg">

                <x-flex class="mt-2">
                    <flux:button wire:click="eliminarImagenCertificado" variant="danger">Eliminar Imagen
                    </flux:button>
                    <x-a href="{{ Storage::disk('public')->url($certificado_actual) }}">Descargar
                    </x-a>
                </x-flex>

                {{-- Componente de subida si no hay imagen alguna --}}
            @else
                <x-subir-file wire:model="certificado" label="Certificado Digital (.pem, .txt)" accept=".pem,.txt" />
            @endif

            @error('certificado')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            {{-- Vista previa cuando hay imagen temporal --}}
            @if ($logo_factura)
                <img src="{{ $logo_factura->temporaryUrl() }}" class="mb-2 max-w-xs max-h-[10rem] rounded-lg shadow-lg"
                    alt="Vista previa de la imagen">
                <flux:button wire:click="eliminarImagen" variant="danger" class="mt-2">Eliminar Imagen</flux:button>

                {{-- Mostrar imagen ya guardada si no hay temporal --}}
            @elseif ($logo_actual)
                <img src="{{ Storage::disk('public')->url($logo_actual) }}"
                    class="mb-2 max-w-xs max-h-[10rem] rounded-lg shadow-lg" alt="Imagen almacenada">
                <flux:button wire:click="eliminarImagen" variant="danger" class="mt-2">Eliminar Imagen</flux:button>

                {{-- Componente de subida si no hay imagen alguna --}}
            @else
                <x-subir-file wire:model="logo_factura" label="Logo para Factura (.jpg, .png)"
                    accept="image/jpeg,image/png" />
            @endif

            @error('logo_factura')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>
</form>
