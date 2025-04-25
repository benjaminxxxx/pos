@props(['variant' => 'info'])

@php
    $colors = [
        'info' => 'bg-blue-100 border-blue-500 text-blue-700',
        'success' => 'bg-green-100 border-green-500 text-green-700',
        'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700',
        'danger' => 'bg-red-100 border-red-500 text-red-700',
    ];
@endphp

<div class="border-l-4 p-4 mb-4 rounded {{ $colors[$variant] ?? $colors['info'] }}">
    <strong>{{ ucfirst($variant) }}:</strong> {{ $slot }}
</div>
