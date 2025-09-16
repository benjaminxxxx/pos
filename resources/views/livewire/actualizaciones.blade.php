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
        ],
        '2025-06-30' => [
            'Se mejoró el diseño general del sistema incorporando un menú lateral colapsable, para aprovechar mejor el espacio en pantallas más pequeñas y facilitar la navegación.',
            'Se agregó un botón debajo de la lista de productos en la venta, para que sea más claro y sencillo agregar más ítems al carrito de compra.',
            'Al hacer clic en el botón para agregar productos, el sistema limpia y enfoca automáticamente el buscador para que sea más fácil y rápido escribir el producto deseado.',
            'El buscador de productos ahora tiene un efecto visual mejorado al escribir o hacer foco, mostrando un resplandor azul animado que guía al usuario sobre dónde debe escribir.',
            'Se incorporó la posibilidad de registrar ventas usando unidades por "rollo", para atender mejor este tipo de productos y permitir mayor flexibilidad en la venta.',
            'En el diseño de la factura se agregó de forma más clara la información del cliente, incluyendo dirección, provincia, distrito y departamento, además del RUC, para estandarizar y completar el formato de facturación.',
            'Se mejoró el diseño de la factura para que la información sea más legible, ordenada y profesional, ayudando a transmitir una imagen más confiable al cliente final.',
            'Se realizaron pequeños ajustes generales para mejorar la usabilidad y claridad de los botones y formularios, facilitando el uso diario del sistema.',
        ],
        '2025-09-10' => [
            'Se mejoró la visibilidad de los títulos en el dashboard para modo claro y oscuro, corrigiendo problemas de contraste.',
            'Las opciones por negocio ahora son más accesibles, se reubicaron para evitar tener que usar scroll innecesario.',
            'Se añadió título al panel de negocios para una mejor identificación.',
            'Se actualizaron los diseños del modo oscuro en: dashboard, listado de productos, módulo de pago y detalle de ventas.',
            'El nombre de la empresa en el voucher se ajustó para que no tape el correlativo.',
            'Se corrigió el hover en modo oscuro que no se apreciaba correctamente.',
            'Se adaptó el cambio de precio al diseño dark mode.',
            'Los íconos fueron redimensionados y se mejoró el diseño del detalle en dark mode.',
            'Se mejoró la estética del menú contraído para mayor usabilidad.',
            'Se agregó la función de vender con “ticket” (nota de venta) sin boleta ni factura, que genera un comprobante no oficial de SUNAT y no incluye detalle de IGV.',
        ],
        '2025-09-16' => [
            'Se agregó el nuevo tipo de negocio "Ganadería" para ampliar la cobertura del sistema.',
            'Se corrigió el formato de los importes (subtotal, IGV y total) mostrando decimales y separador de miles, tanto en la vista como en los documentos generados.',
            'En el TXT de resumen se agregó soporte para decimales en la cantidad y en el subtotal, mejorando la precisión de los registros electrónicos.',
            'Se implementó un sistema de selección de diseño de comprobante, permitiendo definir diseños personalizados con diferentes opciones de ancho y alto según la impresora del cliente.',
            'Ahora si se selecciona el diseño "default", el sistema elimina cualquier personalización previa y aplica el diseño estándar por defecto.',
            'Se agregó lógica automática: si no existe un diseño guardado para un tipo de comprobante, el sistema selecciona automáticamente el diseño por defecto sin necesidad de clic.',
            'Se añadió la relación entre `disenios_disponibles` y `disenios_impresion` para cargar dinámicamente el archivo Blade correcto según negocio, sucursal y tipo de comprobante.',
            'Se agregó un sistema de precios por mayoreo configurable tanto en el producto base como en sus presentaciones.',
            'Se actualizó el formato de precios a "S/. #,##0.00" en los módulos de venta, dashboard y resumen de pago, para mayor consistencia.',
            'Se mejoró la lógica de selección de métodos de pago en el módulo de venta: si el nuevo método cubre el total, reemplaza el anterior; si no, se combina sumando el diferencial hasta completar el total.',
            'Se reorganizó el renderizado de información adicional en comprobantes (cabecera, pie y centro), pasándola como arrays estructurados en lugar de HTML embebido, para mejorar la personalización en las plantillas.',
            'En la generación de PDFs oficiales se estandarizó el uso de tablas en lugar de flexbox para asegurar compatibilidad total con dompdf y evitar problemas de diseño.',
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