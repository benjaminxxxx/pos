@props(['title' => null])

<div class="rounded-lg shadow bg-white p-4 {{ $attributes->get('class') }}">
    @if ($title)
        <div class="border-b pb-2 mb-2 flex justify-between items-center">
            <h3 class="font-bold">{{ $title }}</h3>
            {{ $header ?? '' }} {{-- Espacio para botones o iconos en el header --}}
        </div>
    @endif
    <div class="py-2">
        {{ $slot }}
    </div>
</div>
