<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BoxCenter')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
        <a class="navbar-brand fw-bold" href="{{ auth()->check() ? route('dashboard') : url('/') }}">
            BoxCenter
        </a>

        <div class="ms-auto">
            @auth
                <!-- Menú desplegable de usuario -->
                <div class="dropdown">
                    <button class="btn border-0 p-0" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <img 
                            src="{{ Auth::user()->foto_perfil ?? asset('images/default-avatar.png') }}" 
                            alt="Foto de perfil" 
                            class="rounded-circle border" 
                            style="width: 40px; height: 40px; object-fit: cover;"
                        >
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
                        <li class="px-3 py-2 text-center border-bottom">
                            <strong>{{ Auth::user()->name }}</strong><br>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i> Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ url('/settings') }}">
                                <i class="bi bi-gear me-2"></i> Ajustes
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
            @endauth
        </div>
    </nav>

    <main class="container py-5">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
