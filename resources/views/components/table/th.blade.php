@props(['class' => ''])

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider ' . $class]) }}>
    {{ $slot }}
</th>

