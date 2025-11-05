<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <style>
        /* Sidebar Styles */
        .sidebar {
            width: 82px;
            transition: width 0.3s ease-in-out;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 9999;
            background: #1F2937;
            overflow: hidden;
        }

        .sidebar.expanded {
            width: 18rem;
        }

        /* Menu text animations */
        .menu-text {
            opacity: 0;
            width: 0;
        }

        .sidebar.expanded .menu-text {
            opacity: 1;
            width: auto;
        }

        .sidebar.expanded .hidden-on-expanded {
            opacity: 0;
            width: 0;
        }

        .sidebar.expanded .buton-on-sidebar {
            gap: 0.75rem;
        }

        /* Submenu animations */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .submenu.open {
            max-height: 500px;
        }

        /* Content area adjustment */
        .content-area {
            margin-left: 82px;
            transition: margin-left 0.3s ease-in-out;
            width: calc(100% - 82px);
        }

        .sidebar .ultra-thin-scroll {
            overflow-y: hidden;
        }

        .sidebar.expanded .ultra-thin-scroll {
            overflow-y: auto;
        }


        /* Scroll ultrafino y moderno */
        .ultra-thin-scroll {
            scrollbar-width: thin;
            /* Firefox */
            scrollbar-color: transparent transparent;
        }

        .ultra-thin-scroll:hover {
            scrollbar-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0);
        }

        /* Webkit (Chrome, Edge, Safari) */
        .ultra-thin-scroll::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .ultra-thin-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .ultra-thin-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 9999px;
            border: none;
        }

        .ultra-thin-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, 0.2);
        }


        /* Mobile styles */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                width: 18rem;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .content-area {
                margin-left: 0;
                width: 100%;
            }

            .mobile-overlay {
                position: fixed;
                inset: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9998;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
            }

            .mobile-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <x-sidebar2 />
        <main class="flex-1 overflow-auto p-6 lg:p-8 bg-neutral-200 dark:bg-gray-900">
            <x-cabecera/>
            <div>
                {{ $slot }}
            </div>
        </main>
    </div>
    @fluxScripts
</body>
</html>