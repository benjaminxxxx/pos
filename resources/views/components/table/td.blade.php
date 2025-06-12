@props(['class' => ''])

<td valign="top" {{ $attributes->merge(['class' => 'px-6 py-4 whitespace-nowrap text-md text-gray-600 dark:text-gray-400 ' . $class]) }}>
    {{ $slot }}
</td>

