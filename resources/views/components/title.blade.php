{{-- resources/views/components/title.blade.php --}}
@props([
    'subtitle' => null,   // texto opcional debajo del título
    'level' => 1,         // nivel del encabezado: 1..6
])

@php
    $level = max(1, min(6, (int) $level));
    $tag = 'h' . $level;
@endphp

<div {{ $attributes->merge(['class' => 'module-title flex items-center justify-between gap-4']) }}>
    <div class="title-left flex items-center gap-3">

        <div class="title-text">
            <<?php echo $tag; ?> class="text-lg font-semibold leading-tight">
                {{ $slot }}
            </<?php echo $tag; ?>>

            @if($subtitle)
                <p class="text-sm text-muted mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</div>