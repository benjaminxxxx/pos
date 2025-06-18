<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <!-- Título y Fecha -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard POS</h1>
        <div class="flex items-center gap-2 mt-1">
            <!-- Icono de calendario -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3M16 7V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-gray-600 capitalize">
                {{ \Carbon\Carbon::now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}
            </p>
            <span
                class="ml-2 inline-flex items-center rounded-full bg-gray-200 text-gray-800 px-2 py-0.5 text-xs font-medium dark:bg-neutral-700 dark:text-white">
                Sistema Activo
            </span>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex items-center gap-3">
        <flux:select wire:model.live="filtro" variant="default" placeholder="SELECCIONAR VISTA">
            @foreach ($filtros as $item)
                <flux:select.option value="{{ $item['value'] }}">
                    {{ $item['label'] }}
                </flux:select.option>
            @endforeach
        </flux:select>
    </div>

</div>
