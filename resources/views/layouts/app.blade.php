<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplicación')</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            box-sizing: border-box;
            overflow: hidden; /* evita scroll */
        }

        *, *::before, *::after {
            box-sizing: inherit;
        }
    </style>
    @yield('head')
</head>
<body>
    @yield('content')
</body>
</html>
