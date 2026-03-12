@props(['value'])

<h1  {{ $attributes->merge(['class' => 'font-semibold text-accent text-xl']) }}>
    {{ $value ?? $slot }}
</h1>
