<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')

</head>

<body class="min-h-screen  bg-gray-200 dark:bg-zinc-900">
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.brand logo="{{ asset('image/posicon.png') }}" name="Sistema POS" />
            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            {{-- Dashboard --}}
            <flux:sidebar.item icon="home" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">
                Dashboard
            </flux:sidebar.item>

            {{-- Ventas --}}
            @can('gestionar ventas')
                <flux:sidebar.group expandable heading="Ventas" icon="table-cells"
                    :open="request()->routeIs(['vender', 'ventas','ventas.reporte'])">
                    <flux:sidebar.item href="{{ route('vender') }}" :current="request()->routeIs('vender')">Vender
                    </flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('ventas') }}" :current="request()->routeIs('ventas')">Historial de
                        Ventas</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('ventas.reporte') }}" :current="request()->routeIs('ventas.reporte')">
                        Reporte de Ventas</flux:sidebar.item>
                </flux:sidebar.group>
            @endcan

            {{-- Catálogos --}}
            @can('general')
                <flux:sidebar.group expandable heading="Catálogos" icon="tag"
                    :open="request()->routeIs(['superadmin.clientes', 'superadmin.categorias', 'superadmin.marcas', 'superadmin.unidades'])">
                    <flux:sidebar.item href="{{ route('superadmin.clientes') }}"
                        :current="request()->routeIs('superadmin.clientes')">Clientes</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('superadmin.categorias') }}"
                        :current="request()->routeIs('superadmin.categorias')">Categorías</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('superadmin.marcas') }}"
                        :current="request()->routeIs('superadmin.marcas')">Marcas</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('superadmin.unidades') }}"
                        :current="request()->routeIs('superadmin.unidades')">Unidades</flux:sidebar.item>
                </flux:sidebar.group>
            @endcan

            @role('dueno_tienda')
                {{-- Mi Negocio --}}
                <flux:sidebar.group expandable heading="Mi Negocio" icon="building-office"
                    :open="request()->routeIs(['dueno_tienda.negocios', 'dueno_tienda.sucursales', 'dueno_tienda.clientes', 'dueno_tienda.correlativos', 'dueno_tienda.proveedores'])">
                    <flux:sidebar.item href="{{ route('dueno_tienda.negocios') }}"
                        :current="request()->routeIs('dueno_tienda.negocios')">Mis Negocios</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('dueno_tienda.sucursales') }}"
                        :current="request()->routeIs('dueno_tienda.sucursales')">Sucursales</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('dueno_tienda.clientes') }}"
                        :current="request()->routeIs('dueno_tienda.clientes')">Mis Clientes</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('dueno_tienda.proveedores') }}"
                        :current="request()->routeIs('dueno_tienda.proveedores')">Proveedores</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('dueno_tienda.correlativos') }}"
                        :current="request()->routeIs('dueno_tienda.correlativos')">Correlativos</flux:sidebar.item>
                </flux:sidebar.group>

                {{-- Compras --}}
                <flux:sidebar.group expandable heading="Compras" icon="shopping-cart"
                    :open="request()->routeIs('dueno_tienda.realizar_compras')">
                    <flux:sidebar.item href="{{ route('dueno_tienda.realizar_compras') }}"
                        :current="request()->routeIs('dueno_tienda.realizar_compras')">Registrar Compra</flux:sidebar.item>
                </flux:sidebar.group>

                {{-- Inventario --}}
                <flux:sidebar.group expandable heading="Inventario" icon="archive-box"
                    :open="request()->routeIs(['dueno_tienda.productos', 'dueno_tienda.servicios', 'dueno_tienda.entrada_productos', 'dueno_tienda.salida_productos'])">
                    <flux:sidebar.item href="{{ route('dueno_tienda.productos') }}"
                        :current="request()->routeIs('dueno_tienda.productos')">Productos</flux:sidebar.item>
                    {{-- request()->routeIs('dueno_tienda.servicios')">Servicios --}}
                    <flux:sidebar.item href="{{ route('dueno_tienda.entrada_productos') }}"
                        :current="request()->routeIs('dueno_tienda.entrada_productos')">Entrada Productos
                    </flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('dueno_tienda.salida_productos') }}"
                        :current="request()->routeIs('dueno_tienda.salida_productos')">Salida Productos</flux:sidebar.item>
                </flux:sidebar.group>

                {{-- Movimientos --}}
                <flux:sidebar.group expandable heading="Movimientos" icon="currency-dollar"
                    :open="request()->routeIs(['dueno_tienda.registrar_movimiento', 'dueno_tienda.resumen_mensual_movimientos', 'dueno_tienda.movimientos'])">
                    <flux:sidebar.item href="{{ route('dueno_tienda.movimientos') }}"
                        :current="request()->routeIs('dueno_tienda.movimientos')">Ver Movimientos</flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('dueno_tienda.registrar_movimiento') }}"
                        :current="request()->routeIs('dueno_tienda.registrar_movimiento')">Registrar Movimiento
                    </flux:sidebar.item>
                    <flux:sidebar.item href="{{ route('dueno_tienda.resumen_mensual_movimientos') }}"
                        :current="request()->routeIs('dueno_tienda.resumen_mensual_movimientos')">Resumen Mensual
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- Configuración --}}
                <flux:sidebar.group expandable heading="Configuración" icon="cog-6-tooth"
                    :open="request()->routeIs('dueno_tienda.configuracion.disenio_impresion')">
                    <flux:sidebar.item href="{{ route('dueno_tienda.configuracion.disenio_impresion') }}"
                        :current="request()->routeIs('dueno_tienda.configuracion.disenio_impresion')">Diseños de Impresión
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endrole
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>
            {{-- Enlaces Inferiores --}}
            <flux:sidebar.item icon="folder" href="{{ route('ventas.reporte') }}">Reportes</flux:sidebar.item>
            <flux:sidebar.item icon="check-circle" href="{{ route('actualizaciones') }}"
                :current="request()->routeIs('actualizaciones')">
                Actualizaciones
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:dropdown x-data align="end">
            <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
                <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini"
                    class="text-zinc-500 dark:text-white" />
                <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini"
                    class="text-zinc-500 dark:text-white" />
                <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
                <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
            </flux:button>

            <flux:menu>
                <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        {{-- Perfil de Usuario --}}
        <flux:dropdown position="top" align="start" class="max-lg:hidden">

            <flux:sidebar.profile name="{{ auth()->user()->name }}" />

            <flux:menu>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle" variant="danger">

                        Cerrar Sesión

                    </flux:menu.item>
                </form>

            </flux:menu>

        </flux:dropdown>
        
    </flux:sidebar>



    <flux:header class="lg:hidden">

        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="bottom" align="end">

            {{-- Botón perfil --}}
            <flux:profile :name="Auth::user()->name" :avatar="null" />

            {{-- Menú --}}
            <flux:menu>

                {{-- Información del usuario --}}
                <flux:menu.item disabled>
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-muted-foreground">{{ Auth::user()->email }}</span>
                    </div>
                </flux:menu.item>

                <flux:menu.separator />

                {{-- Configuración --}}
                <flux:menu.item href="{{ route('settings.profile') }}" icon="cog-6-tooth">
                    Configuración
                </flux:menu.item>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle" variant="danger">

                        Salir

                    </flux:menu.item>
                </form>

            </flux:menu>

        </flux:dropdown>

    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
