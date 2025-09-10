{{-- Sidebar2 --}}
<div x-data="{
    isPinned: $persist(false).as('menu_pinned'),
    get isExpanded() {
        return this.isPinned || this._isHovered;
    },
    _isHovered: false,
    openMenus: [],

    handleMouseEnter() {
        this._isHovered = true;
    },
    handleMouseLeave() {
        this._isHovered = false;
        if (!this.isPinned) {
            this.openMenus = [];
        }
    },
    togglePin() {
        this.isPinned = !this.isPinned;
    },
    toggleMenu(title) {
        if (this.openMenus.includes(title)) {
            this.openMenus = this.openMenus.filter(m => m !== title);
        } else {
            this.openMenus.push(title);
        }
    },
    isOpen(title) {
        return this.openMenus.includes(title);
    }
}" x-on:mouseenter="handleMouseEnter" x-on:mouseleave="handleMouseLeave"
    class="relative h-screen bg-white dark:bg-gray-800 border-r dark:border-gray-700 transition-all duration-300 ease-in-out flex flex-col"
    :class="isExpanded ? 'w-64' : 'w-16'">

    <!-- Header -->
    <div class="p-4 border-b dark:border-gray-700 ">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Logo mini -->
                <div :class="isExpanded ? 'w-0 opacity-0' : 'w-8 opacity-100'" class="transition-all duration-300">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center">
                        <img src="{{ asset('image/posicon.png') }}" alt="Logo" />
                    </div>
                </div>
                <!-- Logo expandido -->
                <div :class="isExpanded ? 'w-auto opacity-100' : 'w-0 opacity-0'"
                    class="transition-all duration-300 overflow-hidden">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center">
                            <img src="{{ asset('image/posicon.png') }}" alt="Logo" />
                        </div>
                        <span class="font-semibold text-gray-700 dark:text-white whitespace-nowrap">POSAndino</span>
                    </div>
                </div>
            </div>
            <!-- Pin icon -->
            <button x-show="isExpanded" x-on:click="togglePin"
                class="transition-all duration-300 
           text-gray-600 hover:text-gray-900 
           dark:text-white dark:hover:text-gray-100"
                :class="isPinned ? 'text-gray-400 dark:text-gray-300' : ''">

                <i class="fa fa-thumb-tack h-4 w-4" x-show="!isPinned"></i>
                <i class="fa fa-times-circle h-4 w-4" x-show="isPinned"></i>
            </button>

        </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1  py-4" :class="isExpanded ? 'overflow-y-auto ultra-thin-scroll' : 'overflow-hidden'">
        <nav class="space-y-1 px-2">

            <x-nav-link-simple href="{{ route('dashboard') }}" logo="fa fa-home" text="Dashboard" :active="request()->routeIs('dashboard')" />

            @can('gestionar ventas')
                <x-nav-link-parent name="ventas" :active="request()->routeIs(['vender', 'ventas'])" logo='fa fa-table' text="Ventas">
                    <x-nav-link-child href="{{ route('vender') }}" :active="request()->routeIs('vender')">
                        Vender
                    </x-nav-link-child>
                    <x-nav-link-child href="{{ route('ventas') }}" :active="request()->routeIs('ventas')">
                        Historial de Ventas
                    </x-nav-link-child>

                </x-nav-link-parent>
            @endcan
            @can('general')
                <!-- Catálogos Group -->
                <x-nav-link-parent name="catalogos" logo="fa fa-tag" text="Catálogos" :active="request()->routeIs([
                    'superadmin.clientes',
                    'superadmin.categorias',
                    'superadmin.marcas',
                    'superadmin.unidades',
                ])">
                    <x-nav-link-child href="{{ route('superadmin.clientes') }}" :active="request()->routeIs('superadmin.clientes')">
                        Clientes
                    </x-nav-link-child>

                    <x-nav-link-child href="{{ route('superadmin.categorias') }}" :active="request()->routeIs('superadmin.categorias')">
                        Categorías
                    </x-nav-link-child>

                    <x-nav-link-child href="{{ route('superadmin.marcas') }}" :active="request()->routeIs('superadmin.marcas')">
                        Marcas
                    </x-nav-link-child>

                    <x-nav-link-child href="{{ route('superadmin.unidades') }}" :active="request()->routeIs('superadmin.unidades')">
                        Unidades
                    </x-nav-link-child>
                </x-nav-link-parent>
            @endcan

            @role('dueno_tienda')
                <!-- Gestión de Negocio Group -->
                <x-nav-link-parent name="negocio" logo="fa fa-building" text="Mi Negocio" :active="request()->routeIs([
                    'dueno_tienda.negocios',
                    'dueno_tienda.sucursales',
                    'dueno_tienda.clientes',
                    'dueno_tienda.correlativos',
                ])">
                    <x-nav-link-child href="{{ route('dueno_tienda.negocios') }}" :active="request()->routeIs('dueno_tienda.negocios')">
                        Mis Negocios
                    </x-nav-link-child>

                    <x-nav-link-child href="{{ route('dueno_tienda.sucursales') }}" :active="request()->routeIs('dueno_tienda.sucursales')">
                        Sucursales
                    </x-nav-link-child>

                    <x-nav-link-child href="{{ route('dueno_tienda.clientes') }}" :active="request()->routeIs('dueno_tienda.clientes')">
                        Mis Clientes
                    </x-nav-link-child>

                    <x-nav-link-child href="{{ route('dueno_tienda.correlativos') }}" :active="request()->routeIs('dueno_tienda.correlativos')">
                        Correlativos
                    </x-nav-link-child>
                </x-nav-link-parent>

                <!-- Inventario Group -->
                <x-nav-link-parent name="productos" logo="fa fa-shopping-cart" text="Inventario" :active="request()->routeIs(['dueno_tienda.productos', 'dueno_tienda.servicios'])">
                    <x-nav-link-child href="{{ route('dueno_tienda.productos') }}" :active="request()->routeIs('dueno_tienda.productos')">
                        Productos
                    </x-nav-link-child>

                    <x-nav-link-child href="{{ route('dueno_tienda.servicios') }}" :active="request()->routeIs('dueno_tienda.servicios')">
                        Servicios
                    </x-nav-link-child>
                </x-nav-link-parent>
            @endrole

            <!-- Separator -->
            <div class="my-4 border-t border-zinc-200 dark:border-zinc-700"></div>

            <!-- Reportes (enlace simple) -->
            <x-nav-link-simple href="#" logo="fa fa-folder" text="Reportes" />

            <!-- Actualizaciones -->
            <x-nav-link-simple href="{{ route('actualizaciones') }}" logo="fa fa-check" text="Actualizaciones"
                :active="request()->routeIs('actualizaciones')" />


        </nav>
        <!-- BOTÓN DE MODO CLARO/OSCURO -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <button @click="darkMode = !darkMode"
                class="w-full flex items-center p-2 rounded-lg 
               text-gray-700 hover:bg-gray-100 
               dark:text-white dark:hover:bg-gray-700 transition-colors"
                :class="isExpanded ? 'justify-start' : 'justify-center'">

                <i :class="darkMode ? 'fa fa-moon' : 'fa fa-sun'" class="h-5 w-5"></i>

                <template x-if="isExpanded">
                    <span class="ml-3" x-text="darkMode ? 'Modo Oscuro' : 'Modo Claro'"></span>
                </template>
            </button>
        </div>




        <!-- MENÚ DE USUARIO AL FINAL -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="relative">
                <button onclick="toggleUserMenu()"
                    class="w-full flex items-center p-2 rounded-lg 
           text-gray-700 hover:bg-gray-100 
           dark:text-white dark:hover:bg-gray-800 transition-colors"
                    :class="isExpanded ? 'justify-start' : 'justify-center'">

                    <!-- Avatar -->
                    <div
                        class="w-8 h-8 flex-shrink-0 
               bg-gray-300 text-gray-800 
               dark:bg-gray-600 dark:text-white 
               rounded-lg flex items-center justify-center font-semibold text-sm">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>

                    <!-- Info solo si el menú está expandido -->
                    <template x-if="isExpanded">
                        <div class="flex-1 flex items-center justify-between ml-2">
                            <div class="text-left">
                                <div class="font-semibold text-sm truncate text-gray-900 dark:text-white">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-300 truncate">Empleado</div>
                            </div>
                            <i id="user-chevron" class="fa fa-chevron-up transition-transform flex-shrink-0"></i>
                        </div>
                    </template>
                </button>


                <!-- Dropdown Menu -->
                <div id="user-dropdown"
                    class="absolute bottom-full left-0 w-full mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 opacity-0 invisible transition-all duration-200">
                    <div class="p-2">
                        <!-- Información del usuario -->
                        <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                            <div class="font-semibold text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                        </div>

                        <!-- Opciones -->
                        <a href="{{ route('settings.profile') }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            <i class="fa fa-cogs w-4"></i>
                            <span>Configuración</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md">
                                <i class="fa fa-sign-out-alt w-4"></i>
                                <span>Salir</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        // User menu toggle
        function toggleUserMenu() {
            const dropdown = document.getElementById('user-dropdown');
            const chevron = document.getElementById('user-chevron');

            if (dropdown.classList.contains('opacity-0')) {
                dropdown.classList.remove('opacity-0', 'invisible');
                dropdown.classList.add('opacity-100', 'visible');
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            } else {
                dropdown.classList.add('opacity-0', 'invisible');
                dropdown.classList.remove('opacity-100', 'visible');
                chevron.classList.remove('fa-chevron-down');
                chevron.classList.add('fa-chevron-up');
            }
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = event.target.closest('#user-dropdown') ||
                event.target.closest('button[onclick="toggleUserMenu()"]');

            if (!userMenu) {
                const dropdown = document.getElementById('user-dropdown');
                const chevron = document.getElementById('user-chevron');

                if (dropdown) {
                    dropdown.classList.add('opacity-0', 'invisible');
                    dropdown.classList.remove('opacity-100', 'visible');
                }

                if (chevron) {
                    chevron.classList.remove('fa-chevron-down');
                    chevron.classList.add('fa-chevron-up');
                }
            }
        });
    </script>
</div>
