<flux:modal name="edit-profile" class="md:w-96" wire:model="mostrarFormularioEspecial">
    <form class="space-y-6" wire:submit="guardarUnidadEspecial">
        <div>
            <flux:heading size="lg">Crear unidad especial</flux:heading>
            <flux:text class="mt-2">Las unidades especiales no estan validadas en SUNAT, entonces internamente todas se registran como NIU pero en la descripción aparecera la SUNAT para temas practicos.</flux:text>
        </div>
        <flux:input wire:model="nombre_comercial" label="Unidad Comercial" required />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">Crear Unidad Especial</flux:button>
        </div>
    </form>
</flux:modal>