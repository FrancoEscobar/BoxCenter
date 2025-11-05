<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BoxCenter') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        {{-- Header --}}
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                {{-- Logo sin link --}}
                <span class="text-lg font-semibold text-gray-800 leading-tight">BoxCenter</span>

                {{-- Botón de cerrar sesión --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition">
                        {{ __('Cerrar sesión') }}
                    </button>
                </form>
            </div>
        </nav>

        {{-- Contenido principal --}}
        <main class="py-12">
            @yield('content')
        </main>
    </div>
</body>
</html>
