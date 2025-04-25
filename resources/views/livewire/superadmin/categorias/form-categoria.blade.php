<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <form wire:submit.prevent="guardar">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <flux:input wire:model="descripcion" label="Descripción" />
                    </div>
                    <div class="mb-4">
                        <flux:select wire:model="tipo_negocio" label="Tipo de Negocio">
                            <option value="">Seleccione un tipo</option>
                            @foreach (config('negocios.tipos') as $valor => $nombre)
                                <option value="{{ $valor }}">{{ $nombre }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="mb-4">
                        <flux:select wire:model="categoria_id" label="Categoría Padre (opcional)">
                            <option value="">Ninguna (Categoría Principal)</option>
                            @foreach ($categoriasPadre as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->descripcion }}</option>
                            @endforeach
                        </flux:select>
                    </div>


                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex justify-end gap-4">
                    <flux:button wire:click="closeModal()" type="button">
                        Cancelar
                    </flux:button>
                    <flux:button variant="primary" type="submit" icon="check-circle">
                        Guardar
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
