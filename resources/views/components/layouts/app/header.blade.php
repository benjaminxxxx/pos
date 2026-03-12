<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-base">
    <flux:header container class="border-b border-white/20 bg-teaser">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
            wire:navigate>
            <x-app-logo />
        </a>


        <flux:navbar class="-mb-px max-lg:hidden">
            <x-cabecera />

            @can('gestionar ventas')
                <flux:dropdown>
                    <flux:navbar.item icon="shopping-bag" class="cursor-pointer"
                        :current="request()->routeIs(['ventas','vender'])">
                        Ventas
                    </flux:navbar.item>

                    <flux:menu>
                        <flux:menu.item :href="route('vender')">Vender</flux:menu.item>
                        <flux:menu.item :href="route('ventas')" wire:navigate>Historial de Ventas</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
                <flux:dropdown>
                    <flux:navbar.item icon="presentation-chart-line" class="cursor-pointer"
                        :current="request()->routeIs(['ventas.reporte'])">
                        Reportes
                    </flux:navbar.item>

                    <flux:menu>
                        <flux:menu.item :href="route('ventas.reporte')" wire:navigate>Reporte de Ventas</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            @endcan
            @role('dueno_tienda')
                <flux:dropdown>
                    {{-- Item principal --}}
                    <flux:navbar.item icon="shopping-cart" class="cursor-pointer"
                        :current="request()->routeIs([
                                                                                                                                                            // Inventario
                                                                                                                                                            'dueno_tienda.productos',
                                                                                                                                                            'dueno_tienda.servicios',
                                                                                                                                                            'dueno_tienda.entrada_productos',
                                                                                                                                                            'dueno_tienda.salida_productos',
                                                                                                                                                
                                                                                                                                                            // Movimientos
                                                                                                                                                            'dueno_tienda.movimientos',
                                                                                                                                                            'dueno_tienda.registrar_movimiento',
                                                                                                                                                            'dueno_tienda.resumen_mensual_movimientos',
                                                                                                                                                        ])">
                        Inventario
                    </flux:navbar.item>

                    <flux:menu>

                        <flux:menu.item :href="route('dueno_tienda.productos')" wire:navigate>
                            Productos
                        </flux:menu.item>

                        <flux:menu.item :href="route('dueno_tienda.servicios')" wire:navigate>
                            Servicios
                        </flux:menu.item>

                        <flux:menu.separator />

                        <flux:menu.item :href="route('dueno_tienda.entrada_productos')" wire:navigate>
                            Entrada de Productos
                        </flux:menu.item>

                        <flux:menu.item :href="route('dueno_tienda.salida_productos')" wire:navigate>
                            Salida de Productos
                        </flux:menu.item>

                        <flux:menu.separator />

                        <flux:menu.item :href="route('dueno_tienda.movimientos')" wire:navigate>
                            Ver Movimientos
                        </flux:menu.item>

                        <flux:menu.item :href="route('dueno_tienda.registrar_movimiento')" wire:navigate>
                            Registrar Movimiento
                        </flux:menu.item>

                        <flux:menu.item :href="route('dueno_tienda.resumen_mensual_movimientos')" wire:navigate>
                            Resumen Mensual
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <flux:dropdown>
                    {{-- Item principal --}}
                    <flux:navbar.item icon="building-storefront" class="cursor-pointer"
                        :current="request()->routeIs([
                                                                                                                                    'dueno_tienda.negocios',
                                                                                                                                    'dueno_tienda.sucursales',
                                                                                                                                    'dueno_tienda.clientes',
                                                                                                                                    'dueno_tienda.proveedores',
                                                                                                                                    'dueno_tienda.correlativos',
                                                                                                                                ])">
                        Mi Negocio
                    </flux:navbar.item>

                    <flux:menu>
                        {{-- === NEGOCIOS === --}}
                        <flux:menu.item :href="route('dueno_tienda.negocios')" wire:navigate>
                            Negocios
                        </flux:menu.item>

                        <flux:menu.separator />

                        <flux:menu.item :href="route('dueno_tienda.sucursales')" wire:navigate>
                            Sucursales
                        </flux:menu.item>

                        <flux:menu.item :href="route('dueno_tienda.clientes')" wire:navigate>
                            Clientes
                        </flux:menu.item>

                        <flux:menu.item :href="route('dueno_tienda.proveedores')" wire:navigate>
                            Proveedores
                        </flux:menu.item>

                        <flux:menu.item :href="route('dueno_tienda.correlativos')" wire:navigate>
                            Correlativos
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <flux:dropdown>
                    {{-- Item principal --}}
                    <flux:navbar.item icon="cube" class="cursor-pointer"
                        :current="request()->routeIs([
                                                                                                            'dueno_tienda.realizar_compras',
                                                                                                        ])">
                        Compras
                    </flux:navbar.item>

                    <flux:menu>

                        <flux:menu.item :href="route('dueno_tienda.realizar_compras')" wire:navigate>
                            Registrar Compra
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <flux:dropdown>
                    <flux:navbar.item icon="cog-8-tooth" class="cursor-pointer"
                        :current="request()->routeIs(['dueno_tienda.configuracion.disenio_impresion'])">
                        Configuración
                    </flux:navbar.item>

                    <flux:menu>
                        <flux:menu.item :href="route('dueno_tienda.configuracion.disenio_impresion')" wire:navigate>
                            Diseños de Impresión
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            @endrole

            @can('general')
                <flux:dropdown>
                    {{-- Item principal --}}
                    <flux:navbar.item icon="tag" class="cursor-pointer"
                        :current="request()->routeIs([
                                                            'superadmin.clientes',
                                                            'superadmin.categorias',
                                                            'superadmin.marcas',
                                                            'superadmin.unidades',
                                                        ])">
                        Catálogos
                    </flux:navbar.item>

                    <flux:menu>

                        <flux:menu.item :href="route('superadmin.clientes')" wire:navigate>
                            Clientes
                        </flux:menu.item>



                        <flux:menu.item :href="route('superadmin.categorias')" wire:navigate>
                            Categorías
                        </flux:menu.item>

                        <flux:menu.item :href="route('superadmin.marcas')" wire:navigate>
                            Marcas
                        </flux:menu.item>

                        <flux:menu.item :href="route('superadmin.unidades')" wire:navigate>
                            Unidades
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            @endcan


        </flux:navbar>

        <flux:spacer />

        <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">

            <flux:tooltip content="Actualizaciones" position="bottom">
                <flux:navbar.item class="h-10 max-lg:hidden [&>div>svg]:size-5" icon="book-open-text"
                    href="{{ route('actualizaciones') }}" target="_blank" label="Actualizaciones" />
            </flux:tooltip>
        </flux:navbar>

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            {{-- Dashboard siempre visible --}}
            <flux:navlist.item icon="layout-grid" :href="route('dashboard')"
                :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navlist.item>

            {{-- Ventas --}}
            @can('gestionar ventas')
                <flux:navlist.group heading="Ventas" icon="shopping-bag" expandable
                    :expanded="request()->routeIs(['ventas', 'vender'])">
                    <flux:navlist.item :href="route('vender')">Vender</flux:navlist.item>
                    <flux:navlist.item :href="route('ventas')" wire:navigate>Historial de Ventas</flux:navlist.item>
                    <flux:navlist.item :href="route('ventas.reporte')" wire:navigate>Reporte de Ventas</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            {{-- Inventario y Movimientos (Dueño) --}}
            @role('dueno_tienda')
                <flux:navlist.group heading="Inventario" icon="shopping-cart" expandable
                    :expanded="request()->routeIs(['dueno_tienda.productos', 'dueno_tienda.servicios', 'dueno_tienda.entrada_productos', 'dueno_tienda.salida_productos', 'dueno_tienda.movimientos', 'dueno_tienda.registrar_movimiento', 'dueno_tienda.resumen_mensual_movimientos'])">
                    <flux:navlist.item :href="route('dueno_tienda.productos')" wire:navigate>Productos</flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.servicios')" wire:navigate>Servicios</flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.entrada_productos')" wire:navigate>Entrada de Productos
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.salida_productos')" wire:navigate>Salida de Productos
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.movimientos')" wire:navigate>Ver Movimientos
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.registrar_movimiento')" wire:navigate>Registrar
                        Movimiento</flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.resumen_mensual_movimientos')" wire:navigate>Resumen
                        Mensual</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Mi Negocio" icon="building-storefront" expandable
                    :expanded="request()->routeIs(['dueno_tienda.negocios', 'dueno_tienda.sucursales', 'dueno_tienda.clientes', 'dueno_tienda.proveedores', 'dueno_tienda.correlativos'])">
                    <flux:navlist.item :href="route('dueno_tienda.negocios')" wire:navigate>Negocios</flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.sucursales')" wire:navigate>Sucursales
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.clientes')" wire:navigate>Clientes</flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.proveedores')" wire:navigate>Proveedores
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('dueno_tienda.correlativos')" wire:navigate>Correlativos
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Compras" icon="cube" expandable
                    :expanded="request()->routeIs('dueno_tienda.realizar_compras')">
                    <flux:navlist.item :href="route('dueno_tienda.realizar_compras')" wire:navigate>Registrar Compra
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Configuración" icon="cog-8-tooth" expandable
                    :expanded="request()->routeIs('dueno_tienda.configuracion.disenio_impresion')">
                    <flux:navlist.item :href="route('dueno_tienda.configuracion.disenio_impresion')" wire:navigate>Diseños
                        de Impresión</flux:navlist.item>
                </flux:navlist.group>
            @endrole

            {{-- Catálogos (Admin) --}}
            @can('general')
                <flux:navlist.group heading="Catálogos" icon="tag" expandable
                    :expanded="request()->routeIs(['superadmin.clientes', 'superadmin.categorias', 'superadmin.marcas', 'superadmin.unidades'])">
                    <flux:navlist.item :href="route('superadmin.clientes')" wire:navigate>Clientes</flux:navlist.item>
                    <flux:navlist.item :href="route('superadmin.categorias')" wire:navigate>Categorías</flux:navlist.item>
                    <flux:navlist.item :href="route('superadmin.marcas')" wire:navigate>Marcas</flux:navlist.item>
                    <flux:navlist.item :href="route('superadmin.unidades')" wire:navigate>Unidades</flux:navlist.item>
                </flux:navlist.group>
            @endcan
        </flux:navlist>

        <flux:spacer />

        {{-- Sección inferior --}}
        <flux:navlist variant="outline">
            <flux:navlist.item icon="book-open-text" href="{{ route('actualizaciones') }}" wire:navigate>
                Actualizaciones
            </flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>

    <div class="p-2 md:p-4 lg:p-8">
        {{ $slot }}
    </div>

    @fluxScripts
</body>

</html>
