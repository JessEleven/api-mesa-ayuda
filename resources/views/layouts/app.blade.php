<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Mesa de Ayuda')</title>

        <!-- Se puede usar un PNG o cualquier otro formato -->
        <link rel="icon" href="{{ asset('logo.svg') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link href="{{ asset('tailwind.css') }}" rel="stylesheet">
        @endif
    </head>
    <body class="bg-neutral-800">
        <div class="flex items-center justify-center">
            @yield('content')
        </div>
    </body>
</html>
