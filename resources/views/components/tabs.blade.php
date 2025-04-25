@props(['active' => null])

@php
    $tabs = $attributes->get('tabs');
    $activeTab = $active ?? array_key_first($tabs);
@endphp

<div x-data="{ tab: '{{ $activeTab }}' }" class="w-full">
    <div class="flex space-x-4 border-b border-gray-200 mb-4">
        @foreach ($tabs as $tabName => $label)
            <button
                @click="tab = '{{ $tabName }}'"
                type="button"
                class="px-4 py-2 -mb-px text-sm font-medium border-b-2"
                :class="{
                    'border-primary text-primary': tab === '{{ $tabName }}',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== '{{ $tabName }}'
                }"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
