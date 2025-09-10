@props(['class' => ''])

<h3 {{ $attributes->merge(['class' => 'font-bold text-xl text-gray-700 dark:text-gray-200 ' . $class]) }}>
    {{ $slot }}
</h3>

