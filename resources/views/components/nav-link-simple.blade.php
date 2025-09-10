@props(['href' => '#', 'logo' => '', 'text' => '', 'active' => false])

@php
    $classes =
        $active ?? false
        ? 'rounded w-full h-10 flex items-center transition-all duration-200 
                bg-gray-100 text-gray-900 hover:bg-gray-100 
                dark:bg-gray-700 dark:text-white dark:hover:bg-gray-700'
        : 'rounded w-full h-10 flex items-center transition-all duration-200 
                text-gray-700 hover:bg-gray-100 
                dark:text-gray-200 dark:hover:bg-gray-700';
@endphp

<a href="{{ $href }}" class="{{ $classes }}" :class="isExpanded ? 'justify-start px-3' : 'justify-center px-0'">
    <i class="{{ $logo }} h-5 w-5 flex-shrink-0"></i>
    <template x-if="isExpanded">
        <span class="ml-3 truncate">{{ $text }}</span>
    </template>
</a>
