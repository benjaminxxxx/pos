@props([
    'label' => null,
    'currentFile' => null,
    'accept' => null,
])

<div>
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
        </label>
    @endif

    <div class="flex items-center space-x-2">
        <div class="flex-1">
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label for="{{ $attributes->wire('model')->value }}" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                            <span>Subir archivo</span>
                            <input 
                                id="{{ $attributes->wire('model')->value }}" 
                                {{ $attributes->wire('model') }}
                                type="file" 
                                class="sr-only"
                                @if($accept) accept="{{ $accept }}" @endif
                            >
                        </label>
                        <p class="pl-1">o arrastrar y soltar</p>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Máximo 2MB
                    </p>
                </div>
            </div>
        </div>

        @if($currentFile)
            <div class="flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-md">
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $currentFile }}</span>
            </div>
        @endif
    </div>
</div>

