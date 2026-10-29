<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Canchas El Virrey') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Textos y enlaces de Breeze (grises oscuros por defecto) legibles sobre fondo oscuro */
        .text-gray-600, .text-gray-500, .text-gray-700, .text-gray-900 { color: #a0a0a0 !important; }
        a.underline, a.text-sm { color: #22c55e !important; }
        a.underline:hover { color: #4ade80 !important; }
        input[type="email"], input[type="password"], input[type="text"] { color: #ececec !important; }
        input::placeholder { color: #777 !important; }
    </style>
</head>
<body class="font-sans text-gray-100 antialiased" style="background:#121212;">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background:#121212;">
        <div>
            <a href="/" class="text-2xl font-bold" style="color:#22c55e;">⚽ Canchas El Virrey</a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg"
             style="background:#1e1e1e; border:1px solid #333;">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
