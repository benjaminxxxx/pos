@props([
    'striped' => false,
    'hover' => false,
    'responsive' => true,
    'class' => '',
])

@php
    // Changed table-fixed to table-auto for automatic column width
    $tableClasses = 'w-full text-sm text-left rtl:text-right text-accent' . $class;
 
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
            <thead class="text-xs text-gray-700 uppercase bg-zinc-200 dark:bg-zinc-950 dark:text-white">
                {{ $thead }}
            </thead>
        @endif
        
        @if(isset($tbody))
            <tbody class="bg-zinc-100 dark:bg-zinc-900  dark:text-zinc-100">
                {{ $tbody }}
            </tbody>
        @else
            <tbody class="bg-zinc-100 dark:bg-zinc-900  dark:text-zinc-100">
                {{ $slot }}
            </tbody>
        @endif
    </table>
@if($responsive)
</div>
@endif