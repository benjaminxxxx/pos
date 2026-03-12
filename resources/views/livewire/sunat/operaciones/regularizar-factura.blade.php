<div>
    <flux:modal name="regularizar-factura" class="md:w-[500px]" wire:model="mostrarFormulario">

        <flux:heading size="xl" level="1">
            Regularizar Factura Rechazada
        </flux:heading>

        <flux:subheading>
            Se generará una nueva factura con nuevo correlativo referenciando a la original.
        </flux:subheading>

        {{-- Info de la factura original --}}
        <div class="bg-red-50 border border-red-200 rounded p-3 mt-4">
            <p class="text-sm text-red-700 font-medium">Factura original rechazada</p>
            <p class="text-sm text-red-600">
                {{ $serie_origen }}-{{ $correlativo_origen }} 
                | Fecha: {{ $fecha_origen }}
                | Error: {{ $cdr_descripcion_origen }}
            </p>
        </div>

        <div class="space-y-4 mt-4">

            {{-- Nueva fecha de emisión --}}
            <flux:field>
                <flux:input 
                    type="date" 
                    label="Nueva Fecha de Emisión" 
                    wire:model="nuevaFechaEmision"
                    error="nuevaFechaEmision" />
                <flux:error name="nuevaFechaEmision" />
                <flux:description>
                    Máximo 3 días atrás desde hoy. Para facturas de enero/febrero use fecha actual.
                </flux:description>
            </flux:field>

            {{-- Motivo --}}
            <flux:field>
                <flux:textarea 
                    rows="2" 
                    label="Motivo de Regularización" 
                    wire:model="motivoRegularizacion"
                    placeholder="Regularización de comprobante {{ $serie_origen }}-{{ $correlativo_origen }} de fecha {{ $fecha_origen }} por error de certificado digital."
                    error="motivoRegularizacion" />
                <flux:error name="motivoRegularizacion" />
            </flux:field>

            {{-- Aviso para facturas antiguas --}}
            @if($esFechaAntigua)
                <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                    <p class="text-sm text-yellow-700">
                        ⚠️ Esta factura es de más de 3 días. Se emitirá con fecha de hoy 
                        ({{ now()->format('d/m/Y') }}). Comunique al cliente que debe 
                        actualizar el comprobante en su contabilidad.
                    </p>
                </div>
            @endif
        </div>

        <x-flex class="justify-end mt-6 space-x-2">
            <flux:button wire:click="$set('mostrarFormulario', false)">
                Cancelar
            </flux:button>
            <flux:button variant="primary" icon="save" 
                wire:click="regularizarFactura"
                wire:loading.attr="disabled">
                Generar Nueva Factura
            </flux:button>
        </x-flex>
    </flux:modal>
</div>