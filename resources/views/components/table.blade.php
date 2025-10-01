@props([
    'striped' => false,
    'hover' => false,
    'responsive' => true,
    'class' => '',
])

@php
    // Changed table-fixed to table-auto for automatic column width
    $tableClasses = 'w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 ' . $class;
 
    if ($striped) {
        $tableClasses .= ' table-striped';
    }
    
    if ($hover) {
        $tableClasses .= ' table-hover';
    }
@endphp

@if($responsive)
<div class="relative overflow-x-auto">
@endif
    <table {{ $attributes->merge(['class' => $tableClasses]) }}>
        @if(isset($thead))
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                {{ $thead }}
            </thead>
        @endif
        
        @if(isset($tbody))
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                {{ $tbody }}
            </tbody>
        @else
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                {{ $slot }}
            </tbody>
        @endif
    </table>
@if($responsive)
</div>
@endif