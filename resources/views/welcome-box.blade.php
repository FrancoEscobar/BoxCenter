<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoxCenter</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    <div class="relative min-h-screen flex items-center justify-center text-center">

        <!-- Fondo con imagen -->
        <div 
            class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ asset('images/crossfit-legion.jpg') }}');">
        </div>

        <!-- Overlay oscuro -->
        <div class="absolute inset-0 bg-black/60"></div>

        <!-- Contenido -->
        <div class="relative z-10 px-6 max-w-2xl text-white">

            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Bienvenido a LEGIÓN BOX
            </h1>

            <p class="text-lg md:text-xl mb-10">
                Entrená distinto. Superate cada día.  
                Unite a una comunidad que te empuja a ser mejor.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" 
                   class="px-6 py-3 bg-blue-600 rounded-lg hover:bg-blue-700 transition font-semibold">
                   Iniciar sesión
                </a>

                <a href="{{ route('register') }}" 
                   class="px-6 py-3 bg-white text-gray-900 rounded-lg hover:bg-gray-200 transition font-semibold">
                   Registrarse
                </a>
            </div>

        </div>

    </div>

</body>
</html>
