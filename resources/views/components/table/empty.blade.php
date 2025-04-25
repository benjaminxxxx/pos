@props(['colspan' => 1, 'text' => 'No hay datos disponibles', 'class' => ''])

<tr>
    <td colspan="{{ $colspan }}" {{ $attributes->merge(['class' => 'px-6 py-4 text-center text-gray-500 dark:text-gray-400 ' . $class]) }}>
        {{ $text }}
        @if(isset($slot) && trim($slot))
            <div class="mt-2">
                {{ $slot }}
            </div>
        @endif
    </td>
</tr>

