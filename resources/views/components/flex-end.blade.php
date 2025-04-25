@props(['spacing' => 'space-x-3'])

<div {{ $attributes->merge(['class' => 'flex justify-end ' . $spacing]) }}>
    {{ $slot }}
</div>

