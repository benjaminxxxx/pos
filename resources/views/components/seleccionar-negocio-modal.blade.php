<div>
    @props(['mostrar', 'negocios'])

    @if($mostrar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Seleccionar Negocio</h3>
                    <button wire:click="$set('mostrarModalSeleccionNegocio', false)" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <p class="mb-4 text-sm text-gray-600">
                    Seleccione el negocio con el que desea trabajar:
                </p>
                
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @foreach($negocios as $negocio)
                        <button 
                            wire:click="seleccionarNegocio({{ $negocio->id }})"
                            class="w-full text-left px-4 py-3 border rounded-lg hover:bg-gray-50 flex items-center"
                        >
                            <div class="flex-1">
                                <p class="font-medium">{{ $negocio->nombre_legal }}</p>
                                <p class="text-sm text-gray-500">{{ $negocio->ruc }}</p>
                            </div>
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endforeach
                </div>
                
                @if(count($negocios) === 0)
                    <div class="py-4 text-center text-gray-500">
                        No tiene negocios registrados.
                        <a href="{{ route('dueno_tienda.negocios') }}" class="text-blue-500 hover:underline">Crear un negocio</a>
                    </div>
                @endif
                
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('dueno_tienda.negocios') }}" class="px-4 py-2 text-sm font-medium text-blue-600 hover:underline">
                        Administrar negocios
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

