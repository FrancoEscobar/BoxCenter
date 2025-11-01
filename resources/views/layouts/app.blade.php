<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BoxCenter')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
@livewireScripts
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">BoxCenter</a>
        <div class="ms-auto">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
            @endauth
        </div>
    </nav>

    <main class="container py-5">
        @yield('content')
    </main>

</body>
</html>
