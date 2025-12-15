<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BoxCenter') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    <!-- Fondo con imagen -->
    <div
        class="min-h-screen bg-cover bg-center relative"
        style="background-image: url('{{ asset('images/legion-box.png') }}');"
    >

        <!-- Overlay oscuro -->
        <div class="absolute inset-0 bg-black/80"></div>

        <!-- Contenido -->
        <div class="relative z-10 min-h-screen flex items-center justify-center px-4">

            <!-- Card principal -->
            <div
                class="w-full max-w-lg px-8 py-8
                       bg-white/100
                       shadow-2xl rounded-2xl"
            >

                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <a href="{{ route('welcome.box') }}">
                        <img
                            src="{{ asset('images/logo-legion.png') }}"
                            alt="Legión Box"
                            class="h-32 object-contain"
                        >
                    </a>
                </div>

                <!-- Formulario (slot de Breeze) -->
                {{ $slot }}

            </div>

        </div>
    </div>

</body>
</html>
