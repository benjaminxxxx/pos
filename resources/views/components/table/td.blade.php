@props(['class' => ''])

<td {{ $attributes->merge(['class' => 'px-6 py-4 whitespace-nowrap ' . $class]) }}>
    <div class="text-sm text-gray-500 dark:text-gray-400">
    {{ $slot }}
    </div>
</td>

