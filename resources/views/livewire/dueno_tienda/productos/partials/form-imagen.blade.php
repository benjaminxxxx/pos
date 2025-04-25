<div class="mb-4">
    <flux:heading>Imagen del Producto:</flux:heading>
    <x-subir-file wire:model="imagen" accept="image/*" :temp-url="$imagen_temp_url ?? null" :stored-url="$imagen_url ?? null" />
    @error('imagen')
        <span class="text-red-500 text-xs">{{ $message }}</span>
    @enderror
</div>

@if (isset($imagen_url) && $imagen_url)
    <div class="mb-4">
        <p class="text-sm text-gray-600">Imagen actual:</p>
        <img src="{{$imagen_url}}" alt="Imagen del producto" class="mt-2 max-w-xs">
    </div>
@endif