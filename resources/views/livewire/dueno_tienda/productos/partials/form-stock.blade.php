<div class="mb-4">
    <p class="text-sm text-gray-600 mb-4">Configure el stock inicial para cada sucursal:</p>

    @foreach ($sucursales as $sucursal)
        <flux:card title="{{ $sucursal->nombre }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input 
                    wire:model="stocks.{{ $sucursal->id }}.cantidad" 
                    label="Cantidad" 
                    type="number" 
                    step="0.01"
                />
                
                <flux:input 
                    wire:model="stocks.{{ $sucursal->id }}.stock_minimo" 
                    label="Stock Mínimo" 
                    type="number" 
                    step="0.01"
                />
            </div>
        </flux:card>
    @endforeach

    @if (count($sucursales) === 0)
        <flux:alert variant="warning">
            No hay sucursales registradas. Debe crear al menos una sucursal para gestionar el stock.
        </flux:alert>
    @endif
</div>