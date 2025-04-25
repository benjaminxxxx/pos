@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4 md:p-10 ' . $class]) }}>
    {{ $slot }}
</div>

