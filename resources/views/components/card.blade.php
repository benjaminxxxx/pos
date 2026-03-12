@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'p-6 bg-card border border-border rounded-lg shadow-sm ' . $class]) }}>
    {{ $slot }}
</div>
