<x-table responsive>
    <x-slot name="thead">
        <x-table.tr>
            <x-table.th>Tipo y Documento</x-table.th>
            <x-table.th>Nombre Legal / Comercial</x-table.th>
            <x-table.th>Ubicación</x-table.th>
            <x-table.th>Contacto a enviar</x-table.th>
            <x-table.th class="text-center">Acciones</x-table.th>
        </x-table.tr>
    </x-slot>

    <x-slot name="tbody">
        @foreach ($clientes as $cliente)
            <x-table.tr>
                <x-table.td>
                    {{ ucfirst($cliente->tipo_cliente_id) }}
                    <p>Doc:  {{ $cliente->numero_documento }}</p>
                </x-table.td>

                <x-table.td>
                    <b>Razon Social:</b>
                    <p>{{ $cliente->nombre_completo }}</p>
                    <b>Nombre Comercial:</b>
                    <p>{{ $cliente->nombre_comercial }}</p>
                </x-table.td>
                <x-table.td>
                    @if ($cliente->direccion)
                        <p>{{ $cliente->direccion }}</p>
                    @endif
                    @if ($cliente->distrito)
                        <p>{{ $cliente->distrito }}</p>
                    @endif
                    @if ($cliente->provincia)
                        <p>{{ $cliente->provincia }}</p>
                    @endif
                    @if ($cliente->departamento)
                        <p>{{ $cliente->departamento }}</p>
                    @endif
                </x-table.td>
                <x-table.td>
                    <p>Email: {{ $cliente->email }}</p>
                    <p>Tel.: {{ $cliente->telefono }}</p>
                    <p>Whatsapp: {{ $cliente->whatsapp }}</p>
                </x-table.td>
                <x-table.td class="text-center">
                    <div class="flex space-x-2 justify-center">
                        <flux:button wire:click="editarCliente({{ $cliente->id }})" variant="outline" icon="pencil"
                            size="sm">
                            Editar
                        </flux:button>
                        <flux:button wire:click="eliminarCliente({{ $cliente->id }})"
                            wire:confirm="¿Está seguro de eliminar este cliente?" variant="danger" size="sm"
                            icon="trash">
                            Eliminar
                        </flux:button>
                    </div>
                </x-table.td>
            </x-table.tr>
        @endforeach
    </x-slot>
</x-table>

<div class="mt-5">
    {{ $clientes->links() }}
</div>
