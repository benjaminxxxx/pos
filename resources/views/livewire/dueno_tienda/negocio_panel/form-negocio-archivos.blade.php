<div class="grid grid-cols-1 md:grid-cols-2 gap-10">

    {{-- CERTIFICADO --}}
    <div>

        <h3 class="font-semibold mb-3">
            Certificado Digital
        </h3>

        @if ($certificado)
            <img src="{{ asset('image/pemfile.png') }}" class="mb-2 max-h-[10rem] rounded-lg shadow">

            <flux:button wire:click="eliminarImagenCertificado" variant="danger">
                Eliminar
            </flux:button>
        @elseif ($certificado_actual)
            <img src="{{ asset('image/pemfile.png') }}" class="mb-2 max-h-[10rem] rounded-lg shadow">

            <div class="flex gap-3">

                <flux:button wire:click="eliminarImagenCertificado" variant="danger">
                    Eliminar
                </flux:button>

                <x-a href="{{ Storage::disk('public')->url($certificado_actual) }}">
                    Descargar
                </x-a>

            </div>
        @else
            <x-subir-file wire:model="certificado" label="Certificado (.pem)" accept=".pem,.txt" />
        @endif

    </div>


    {{-- LOGO --}}
    <div>

        <h3 class="font-semibold mb-3">
            Logo Factura
        </h3>

        @if ($logo_factura)
            <img src="{{ $logo_factura->temporaryUrl() }}" class="mb-2 max-h-[10rem] rounded-lg shadow">

            <flux:button wire:click="eliminarImagen" variant="danger">
                Eliminar
            </flux:button>
        @elseif ($logo_actual)
            <img src="{{ Storage::disk('public')->url($logo_actual) }}" class="mb-2 max-h-[10rem] rounded-lg shadow">

            <flux:button wire:click="eliminarImagen" variant="danger">
                Eliminar
            </flux:button>
        @else
            <x-subir-file wire:model="logo_factura" label="Logo (.jpg .png)" accept="image/jpeg,image/png" />
        @endif

    </div>

</div>
