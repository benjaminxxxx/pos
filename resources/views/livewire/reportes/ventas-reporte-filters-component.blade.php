<x-card>
    <div class="flex items-center gap-2 mb-4">
        {{-- Icono Filter de Font Awesome --}}
        <i class="fa-solid fa-filter text-slate-600 dark:text-white"></i>
        <flux:heading>Filtros</flux:heading>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Sucursal --}}
        <div>
            <flux:select label="Sucursal" wire:model.live="filters.sucursal">
                <flux:select.option value="all">Todas las sucursales</flux:select.option>
                @foreach ($sucursales as $sucursal)
                    <flux:select.option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        {{-- Fecha Inicio --}}
        <div>
            <flux:input type="date" label="Fecha Inicio" wire:model.live="filters.fechaInicio" />
        </div>

        {{-- Fecha Fin --}}
        <div>
            <flux:input type="date" label="Fecha Fin" wire:model.live="filters.fechaFin" />
        </div>

    </div>
</x-card>
