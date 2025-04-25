<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="bg-background antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div class="flex justify-center items-center min-h-screen">
        <div
            class="bg-white rounded-2xl p-2 md:p-10 shadow-lg w-full lg:max-w-[1024px] border-8 border-gray-100 dark:border-neutral-900 dark:bg-neutral-800">
            {{ $slot }}
        </div>
    </div>
    @fluxScripts
</body>

</html>
