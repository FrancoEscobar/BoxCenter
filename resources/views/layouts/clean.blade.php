<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'BoxCenter'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    @livewireStyles
</head>
<body style="background-color: #f8f9fa;">
    <div style="min-height: 100vh;">
        {{-- Header --}}
        <nav class="navbar navbar-light bg-white border-bottom">
            <div class="container-fluid">
                {{-- Logo --}}
                <a class="navbar-brand" href="#">
                    <img src="{{ asset('images/logo1-legion.jpeg') }}" alt="BoxCenter" style="height: 40px;">
                </a>

                {{-- Botón de cerrar sesión --}}
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </nav>

        {{-- Contenido principal --}}
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
