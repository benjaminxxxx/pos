<div x-data="disenioImpresion">
    <x-loading wire:loading />

    <x-card class="p-6 space-y-6">
        <h2 class="text-2xl font-bold">Configuración de Diseños de Impresión</h2>
        <p class="text-muted-foreground">Selecciona el diseño predeterminado por negocio y sucursal</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Paso 1: Seleccionar Negocio --}}
            <x-card>
                <h2 class="flex items-center gap-2 text-card-foreground text-lg font-semibold">
                    <i class="fa fa-building h-5 w-5 text-primary"></i>
                    1. Seleccionar Negocio
                </h2>

                <div class="space-y-3">
                    @foreach($negocios as $negocio)
                                    <button type="button" wire:click="seleccionarNegocio({{ $negocio->id }})"
                                        class="w-full flex items-center justify-between h-auto p-4 rounded-md border
                                                                                                                                               {{ $selectedNegocio == $negocio->id
                        ? 'bg-indigo-500 text-white border-indigo-600'
                        : 'bg-white hover:bg-muted border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600'
                                                                                                                                               }}">
                                        <div class="text-left">
                                            <div class="font-semibold">{{ $negocio->nombre_legal }}</div>
                                            <div class="text-sm opacity-70">{{ $negocio->nombre_comercial }}</div>
                                        </div>
                                        @if($selectedNegocio == $negocio->id)
                                            <x-icon name="check" class="h-4 w-4 text-white" />
                                        @endif
                                    </button>
                    @endforeach
                </div>
            </x-card>
            {{-- Paso 2: Seleccionar Sucursal --}}
            <x-card>
                <h2 class="flex items-center gap-2 text-card-foreground text-lg font-semibold">
                    <i class="fa fa-map-pin h-5 w-5 text-primary"></i>
                    2. Seleccionar Sucursal
                </h2>

                <div class="space-y-3">
                    @forelse($sucursales as $sucursal)
                                    <button type="button" wire:click="$set('selectedSucursal', {{ $sucursal->id }})" class="w-full flex items-center justify-between h-auto p-4 rounded-md border
                                                                                                       {{ $selectedSucursal == $sucursal->id
                        ? 'bg-indigo-500 text-white border-indigo-600'
                        : 'bg-white hover:bg-muted border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600'
                                                                                                       }}">
                                        <div class="text-left">
                                            <div class="font-semibold">{{ $sucursal->nombre }}</div>
                                            <div class="text-sm opacity-70">{{ $sucursal->direccion }}</div>
                                        </div>
                                        @if($selectedSucursal == $sucursal->id)
                                            <x-icon name="check" class="h-4 w-4 text-white" />
                                        @endif
                                    </button>
                    @empty
                        <p class="text-muted-foreground text-center py-8">Primero selecciona un negocio</p>
                    @endforelse
                </div>
            </x-card>
            <x-card>
                <h2 class="flex items-center gap-2 text-card-foreground text-lg font-semibold">
                    <i class="fa fa-file-text h-5 w-5 text-primary"></i>
                    3. Tipo de Documento
                </h2>

                <div class="space-y-3">
                    @if($selectedSucursal)
                        @foreach($tiposComprobante as $tipo)
                                    <button type="button" wire:click="seleccionarTipoComprobante('{{ $tipo->codigo }}')" class="w-full flex items-center justify-between h-auto p-4 rounded-md border
                                                                        {{ $selectedTipoComprobante == $tipo->codigo
                            ? 'bg-indigo-500 text-white border-indigo-600'
                            : 'bg-white hover:bg-muted border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600'
                                                                        }}">

                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold">{{ $tipo->descripcion }}</span>
                                        </div>

                                        @if($selectedTipoComprobante == $tipo->codigo)
                                            <x-icon name="check" class="h-4 w-4 text-white" />
                                        @endif
                                    </button>
                        @endforeach
                    @else
                        <p class="text-muted-foreground text-center py-8">
                            Primero selecciona una sucursal
                        </p>
                    @endif
                </div>
            </x-card>

        </div>



        {{-- Paso 4: Diseño --}}
        @if($selectedTipoComprobante)
            <x-card class="mt-6">
                <h2 class="flex items-center gap-2 text-card-foreground text-lg font-semibold mb-4">
                    <i class="fa fa-print h-5 w-5 text-primary"></i>
                    4. Seleccionar Diseño Predeterminado
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div wire:click="seleccionarDiseno('default')" class="cursor-pointer rounded-lg border p-4 transition-all
                                        {{ $selectedDiseno === 'default'
            ? 'ring-2 ring-primary bg-primary/5 border-primary'
            : 'hover:bg-muted border-gray-300 dark:border-gray-600' }}">

                        <div class="aspect-[4/5] bg-muted rounded-md mb-3 overflow-hidden flex items-center justify-center">
                            <img src="{{ asset('image/default.png') }}" alt="Diseño por Defecto"
                                class="w-full h-full object-cover" />
                        </div>

                        <h3 class="font-semibold text-sm mb-1">Default</h3>
                        <span class="px-2 py-1 text-xs rounded bg-gray-200 dark:bg-gray-700">
                            Standard
                        </span>

                        @if($selectedDiseno === 'default')
                            <div class="mt-2 flex items-center justify-center">
                                <span class="px-2 py-1 text-xs rounded bg-indigo-500 text-white flex items-center gap-1">
                                    <i class="fa fa-check"></i> Seleccionado
                                </span>
                            </div>
                        @endif
                    </div>

                    @foreach($disenosDisponibles as $diseno)
                            <div wire:click="seleccionarDiseno({{ $diseno->id }})" class="cursor-pointer rounded-lg border p-4 transition-all
                                                {{ $selectedDiseno === $diseno->id
                        ? 'ring-2 ring-primary bg-primary/5 border-primary'
                        : 'hover:bg-muted border-gray-300 dark:border-gray-600' }}">

                                <div class="aspect-[4/5] bg-muted rounded-md mb-3 overflow-hidden flex items-center justify-center">
                                    <img src="{{  asset($diseno->preview) }}" alt="{{ $diseno->descripcion }}"
                                        class="w-full h-full object-cover" />
                                </div>

                                <h3 class="font-semibold text-sm mb-1">{{ $diseno->descripcion }}</h3>
                                <span class="px-2 py-1 text-xs rounded bg-gray-200 dark:bg-gray-700">
                                    {{ $diseno->codigo }}
                                </span>

                                @if($selectedDiseno === $diseno->id)
                                    <div class="mt-2 flex items-center justify-center">
                                        <span class="px-2 py-1 text-xs rounded bg-indigo-500 text-white flex items-center gap-1">
                                            <i class="fa fa-check"></i> Seleccionado
                                        </span>
                                    </div>
                                @endif
                            </div>
                    @endforeach
                </div>
            </x-card>
        @endif
    </x-card>
</div>
@script
<script>
    Alpine.data('disenioImpresion', () => ({
        init: function () {

        }
    }));
</script>
@endscript