<div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
        {{-- Icono BarChart3 equivalente en FA --}}
        <i class="fa-solid fa-chart-line text-3xl"></i>

        <div>
            <flux:heading size="xl">Reportes de Ventas</flux:heading>
            <flux:subheading>Análisis y métricas de desempeño</flux:subheading>
        </div>
    </div>

    <div class="flex gap-3">
        {{-- Botón Exportar Excel --}}
        <flux:button variant="outline" size="sm" wire:click="exportExcel"
            class="!bg-transparent !text-white !border-slate-600 hover:!bg-slate-700">
            <i class="fa-solid fa-file-excel mr-2"></i>
            Exportar Excel
        </flux:button>

        {{-- Botón Exportar PDF --}}
        <flux:button variant="outline" size="sm" wire:click="exportPdf"
            class="!bg-transparent !text-white !border-slate-600 hover:!bg-slate-700">
            <i class="fa-solid fa-file-pdf mr-2"></i>
            Exportar PDF
        </flux:button>
    </div>
</div>
