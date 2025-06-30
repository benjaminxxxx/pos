<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <style>
        .sidebar-collapsed {
            width: 4rem;
        }
        .sidebar-expanded {
            width: 16rem;
        }
        .sidebar-transition {
            transition: width 0.3s ease-in-out;
        }
        .menu-label {
            opacity: 0;
            transform: translateX(-10px);
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
        }
        .sidebar-expanded .menu-label {
            opacity: 1;
            transform: translateX(0);
        }
        .submenu-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        .submenu-expanded {
            max-height: 500px;
        }
        @media (max-width: 1024px) {
            .sidebar-collapsed {
                width: 0;
            }
            .sidebar-expanded {
                width: 16rem;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar-collapsed sidebar-transition bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700 flex flex-col relative z-50">
            
            <!-- Logo Section -->
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center">
                    <div class="w-8 h-8 flex-shrink-0">
                        <x-app-logo />
                    </div>
                    <span class="menu-label ml-3 font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                        Sistema POS
                    </span>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 py-4 overflow-y-auto">
                <div class="px-2 space-y-1">
                    
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                      >
                        <flux:icon.home class="w-5 h-5 flex-shrink-0" />
                        <span class="menu-label ml-3 whitespace-nowrap">{{ __('Dashboard') }}</span>
                    </a>

                    @can('gestionar ventas')
                    <!-- Ventas Group -->
                    <div class="menu-group">
                        <button onclick="toggleSubmenu('ventas')" 
                                class="w-full flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <flux:icon.shopping-cart class="w-5 h-5 flex-shrink-0" />
                            <span class="menu-label ml-3 flex-1 text-left whitespace-nowrap">Ventas</span>
                            <flux:icon.chevron-right class="menu-label w-4 h-4 transition-transform" id="ventas-chevron" />
                        </button>
                        <div id="ventas-submenu" class="submenu-container ml-8 mt-1">
                            <a href="{{ route('vender') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('vender') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Nueva Venta
                            </a>
                            <a href="{{ route('ventas') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('ventas') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Historial de Ventas
                            </a>
                        </div>
                    </div>
                    @endcan

                    @can('general')
                    <!-- Catálogos Group -->
                    <div class="menu-group">
                        <button onclick="toggleSubmenu('catalogos')" 
                                class="w-full flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <flux:icon.tag class="w-5 h-5 flex-shrink-0" />
                            <span class="menu-label ml-3 flex-1 text-left whitespace-nowrap">Catálogos</span>
                            <flux:icon.chevron-right class="menu-label w-4 h-4 transition-transform" id="catalogos-chevron" />
                        </button>
                        <div id="catalogos-submenu" class="submenu-container ml-8 mt-1">
                            <a href="{{ route('superadmin.clientes') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('superadmin.clientes') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Clientes
                            </a>
                            <a href="{{ route('superadmin.categorias') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('superadmin.categorias') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Categorías
                            </a>
                            <a href="{{ route('superadmin.marcas') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('superadmin.marcas') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Marcas
                            </a>
                            <a href="{{ route('superadmin.unidades') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('superadmin.unidades') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Unidades
                            </a>
                        </div>
                    </div>
                    @endcan

                    @role('dueno_tienda')
                    <!-- Gestión de Negocio Group -->
                    <div class="menu-group">
                        <button onclick="toggleSubmenu('negocio')" 
                                class="w-full flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <flux:icon.building-office class="w-5 h-5 flex-shrink-0" />
                            <span class="menu-label ml-3 flex-1 text-left whitespace-nowrap">Mi Negocio</span>
                            <flux:icon.chevron-right class="menu-label w-4 h-4 transition-transform" id="negocio-chevron" />
                        </button>
                        <div id="negocio-submenu" class="submenu-container ml-8 mt-1">
                            <a href="{{ route('dueno_tienda.negocios') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('dueno_tienda.negocios') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Mis Negocios
                            </a>
                            <a href="{{ route('dueno_tienda.sucursales') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('dueno_tienda.sucursales') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Sucursales
                            </a>
                            <a href="{{ route('dueno_tienda.clientes') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('dueno_tienda.clientes') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Mis Clientes
                            </a>
                            <a href="{{ route('dueno_tienda.correlativos') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('dueno_tienda.correlativos') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Correlativos
                            </a>
                        </div>
                    </div>

                    <!-- Productos y Servicios Group -->
                    <div class="menu-group">
                        <button onclick="toggleSubmenu('productos')" 
                                class="w-full flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <flux:icon.shopping-cart class="w-5 h-5 flex-shrink-0" />
                            <span class="menu-label ml-3 flex-1 text-left whitespace-nowrap">Inventario</span>
                            <flux:icon.chevron-right class="menu-label w-4 h-4 transition-transform" id="productos-chevron" />
                        </button>
                        <div id="productos-submenu" class="submenu-container ml-8 mt-1">
                            <a href="{{ route('dueno_tienda.productos') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('dueno_tienda.productos') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Productos
                            </a>
                            <a href="{{ route('dueno_tienda.servicios') }}" 
                               class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 {{ request()->routeIs('dueno_tienda.servicios') ? 'text-blue-600 dark:text-blue-400' : '' }}"
                              >
                                Servicios
                            </a>
                        </div>
                    </div>
                    @endrole

                    <!-- Separator -->
                    <div class="my-4 border-t border-zinc-200 dark:border-zinc-700"></div>

                    <!-- Additional Links -->
                    <a href="#" 
                       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <flux:icon.folder-git-2 class="w-5 h-5 flex-shrink-0" />
                        <span class="menu-label ml-3 whitespace-nowrap">Reportes</span>
                    </a>

                    <a href="{{ route('actualizaciones') }}" 
                       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('actualizaciones') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                      >
                        <flux:icon.check class="w-5 h-5 flex-shrink-0" />
                        <span class="menu-label ml-3 whitespace-nowrap">Actualizaciones</span>
                    </a>
                </div>
            </nav>

            <!-- User Menu at Bottom -->
            <div class="border-t border-zinc-200 dark:border-zinc-700 p-2">
                <flux:dropdown position="top" align="start" class="w-full">
                    <button class="w-full flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <div class="w-8 h-8 flex-shrink-0 bg-neutral-200 dark:bg-neutral-700 rounded-lg flex items-center justify-center text-black dark:text-white font-semibold">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="menu-label ml-3 flex-1 text-left">
                            <div class="font-semibold truncate">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</div>
                        </div>
                        <flux:icon.chevrons-up-down class="menu-label w-4 h-4 flex-shrink-0" />
                    </button>

                    <flux:menu class="w-[220px]">
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
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
                            <flux:menu.item :href="route('settings.profile')" icon="cog">
                                Configuración
                            </flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                Salir
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>

        <!-- Mobile Header -->
        <flux:header class="lg:hidden">
            <button onclick="toggleMobileSidebar()" class="p-2">
                <flux:icon.bars-2 class="w-6 h-6" />
            </button>
            <flux:spacer />
            <!-- Mobile user menu here if needed -->
        </flux:header>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <script>
        // Sidebar hover functionality
        const sidebar = document.getElementById('sidebar');
        let hoverTimeout;

        sidebar.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimeout);
            if (window.innerWidth >= 1024) { // Only on desktop
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
            }
        });

        sidebar.addEventListener('mouseleave', function() {
            if (window.innerWidth >= 1024) { // Only on desktop
                hoverTimeout = setTimeout(() => {
                    sidebar.classList.remove('sidebar-expanded');
                    sidebar.classList.add('sidebar-collapsed');
                }, 300);
            }
        });

        // Submenu toggle functionality
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById(menuId + '-submenu');
            const chevron = document.getElementById(menuId + '-chevron');
            
            if (submenu.classList.contains('submenu-expanded')) {
                submenu.classList.remove('submenu-expanded');
                chevron.style.transform = 'rotate(0deg)';
            } else {
                submenu.classList.add('submenu-expanded');
                chevron.style.transform = 'rotate(90deg)';
            }
        }

        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            
            if (sidebar.classList.contains('sidebar-expanded')) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                overlay.classList.remove('hidden');
            }
        }

        // Close mobile sidebar when clicking overlay
        document.getElementById('mobile-overlay').addEventListener('click', toggleMobileSidebar);

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                document.getElementById('mobile-overlay').classList.add('hidden');
            }
        });
    </script>

    @fluxScripts
</body>
</html>