<div>
    <flux:modal name="edit-profile" class="md:w-96" wire:model="mostrarFormulario">

        <flux:heading size="xl" level="1">
            Emitir Nota de Crédito
        </flux:heading>

        <div class="space-y-4">
            <!-- Fecha de Emisión -->
            <flux:field class="mt-4">

                <flux:input type="date" label="Fecha de Emisión" wire:model="fechaEmision"
                    placeholder="Seleccione la fecha de emisión" error="fechaEmision" />

                <flux:error name="fechaEmision" />
            </flux:field>
            <flux:field>
                <flux:select label="Tipo de Nota de Crédito" wire:model.live="tipoNota"
                    placeholder="Seleccione el motivo" error="tipoNota">
                    @foreach ($catalogo9 as $motivo)
                        <flux:select.option value="{{ $motivo->codigo }}">{{ $motivo->descripcion }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="tipoNota" />
            </flux:field>
            <flux:field>
                <flux:label>
                    Número de FE respecto de la cual se emite la Nota de Crédito
                </flux:label>
            </flux:field>

            <flux:field class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <flux:input wire:model.live="serie_comprobante" placeholder="F001" error="serie" />
                <flux:input wire:model.live="correlativo_comprobante" placeholder="1" error="correlativo" />
                <flux:link variant="subtle" target="_blank"
                    href="{{ route('ver_factura', ['serie' => $serie_comprobante, 'numero' => $correlativo_comprobante]) }}">
                    Ver Factura
                </flux:link>


            </flux:field>

            <!-- Motivo o Sustento -->
            <div>
                <flux:textarea rows="1" label="Motivo o Sustento" wire:model="motivo" error="motivo" />
            </div>
        </div>

        <x-flex class="justify-end mt-6 space-x-2">

            <flux:button variant="primary" icon="save" wire:click="generarNotaCredito">
                Generar
            </flux:button>
        </x-flex>
    </flux:modal>
    <x-loading wire:loading />
</div>
