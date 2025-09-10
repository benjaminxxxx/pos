@props(['class' => ''])

<p {{ $attributes->merge(['class' => 'font-normal text-gray-700 dark:text-gray-400 ' . $class]) }}>
    {{ $slot }}
</p>

