@props([
    'label' => null,
    'id' => $attributes->get('id') ?? 'select-' . uniqid(),
    'size' => 'default', // small | default | large
    'disabled' => false,
])

@php
    $padding = match($size) {
        'small' => 'p-2 text-sm',
        'large' => 'px-4 py-3 text-base',
        default => 'p-2.5 text-sm',
    };
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $id }}"
        @disabled($disabled)
        {{ $attributes->merge([
            'class' => "bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500
                        block w-full $padding
                        dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white
                        dark:focus:ring-blue-500 dark:focus:border-blue-500"
        ]) }}
    >
        {{ $slot }}
    </select>
</div>
