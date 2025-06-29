@php
$actualizaciones = [
    '2025-06-21' => [
        'Ahora puedes buscar los datos del cliente por RUC directamente desde SUNAT para completar la información de forma más precisa y rápida.',
        'Se agregó el nuevo menú "Actualizaciones", donde podrás ver una interfaz moderna con todos los cambios y mejoras recientes del sistema.',
        'Se añadió el menú "Clientes", desde donde como dueño de tienda podrás registrar, editar y consultar los clientes atendidos en ventas.',
        'Se corrigió un problema en la lista de últimas compras: ahora se muestran correctamente ordenadas por fecha de emisión.',
        'Se mejoró el formulario de registro de clientes para que todos los datos relevantes se envíen correctamente a SUNAT, usando el nombre legal (Razón Social) en lugar del nombre comercial.',
        'El formulario rápido de clientes desde la venta ahora admite todos los campos necesarios como dirección, teléfono, correo, etc., y se adapta según el tipo de documento.',
        'Se mejoró la validación del RUC para evitar consultas erróneas o incompletas.',
        'Ahora el botón "Buscar SUNAT" muestra un estado de carga mientras se realiza la consulta, para mayor claridad.',
        'Se aplicaron mejoras internas de seguridad y rendimiento general en el registro y búsqueda de clientes.',
    ]
];


// Ordenar por fecha descendente (clave)
$actualizaciones = collect($actualizaciones)->sortKeysDesc();
@endphp

<div class="container p-6">
    <h2 class="text-2xl font-bold mb-6">📝 Actualizaciones del Sistema</h2>

    @foreach ($actualizaciones as $fecha => $items)
        <div class="mb-6">
            <h3 class="text-primary text-sm font-semibold border-b border-blue-200 pb-1">
                {{ \Carbon\Carbon::parse($fecha)->translatedFormat('l d \d\e F, Y') }}
            </h3>
            <ul class="mt-2 space-y-2 list-disc list-inside text-sm text-gray-800 dark:text-gray-200">
                @foreach ($items as $descripcion)
                    <li>{{ $descripcion }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
