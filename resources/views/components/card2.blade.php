@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'p-2 md:p-3 bg-card-muted border-base border rounded-lg shadow-sm  ' . $class]) }}>
    {{ $slot }}
</div>

