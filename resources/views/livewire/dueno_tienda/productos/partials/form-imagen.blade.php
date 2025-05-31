<div class="mb-4">
    <flux:heading>Imagen del Producto:</flux:heading>

    {{-- Vista previa cuando hay imagen temporal --}}
    @if ($imagen)
        <img src="{{ $imagen->temporaryUrl() }}" class="mb-2 max-w-xs max-h-[10rem] rounded-lg shadow-lg" alt="Vista previa de la imagen">
        <flux:button wire:click="eliminarImagen" variant="danger" class="mt-2">Eliminar Imagen</flux:button>

    {{-- Mostrar imagen ya guardada si no hay temporal --}}
    @elseif ($imagen_url)
        <img src="{{ Storage::disk('public')->url($imagen_url) }}" class="mb-2 max-w-xs max-h-[10rem] rounded-lg shadow-lg" alt="Imagen almacenada">
        <flux:button wire:click="eliminarImagen" variant="danger" class="mt-2">Eliminar Imagen</flux:button>

    {{-- Componente de subida si no hay imagen alguna --}}
    @else
        <x-subir-file wire:model="imagen" accept="image/*" />
    @endif

    @error('imagen')
        <span class="text-red-500 text-xs">{{ $message }}</span>
    @enderror
</div>

