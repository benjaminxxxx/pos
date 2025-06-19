<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>
<body class="bg-background antialiased dark:bg-neutral-900 h-screen overflow-auto">
    <div class="lg:flex lg:justify-center lg:items-center lg:h-screen">
        <div class="w-full h-screen lg:h-auto lg:rounded-2xl p-5 lg:p-10 lg:shadow-lg lg:max-w-[1024px] lg:border-8 lg:border-gray-100 lg:dark:border-neutral-900 bg-white dark:bg-neutral-800">
            {{ $slot }}
        </div>
    </div>
    @fluxScripts
</body>
</html>
