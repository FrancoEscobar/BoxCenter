<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoxCenter</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    <div class="relative min-h-screen flex items-center justify-center text-center bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900">

        <div 
            class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ asset('images/crossfit-legion.jpg') }}');">
        </div>

        <!-- Overlay con patrón -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>

        <!-- Contenido -->
        <div class="relative z-10 px-4 sm:px-6 py-8 max-w-2xl text-white">

            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 sm:mb-6 leading-tight">
                Bienvenido a LEGIÓN BOX
            </h1>

            <p class="text-base sm:text-lg md:text-xl mb-8 sm:mb-10 leading-relaxed px-2">
                Entrená distinto. Superate cada día.  
                Unite a una comunidad que te empuja a ser mejor.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center w-full max-w-md mx-auto">
                <a href="{{ route('login') }}" 
                   class="px-8 py-3.5 bg-blue-600 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-semibold text-base sm:text-lg shadow-lg">
                   Iniciar sesión
                </a>

                <a href="{{ route('register') }}" 
                   class="px-8 py-3.5 bg-white text-gray-900 rounded-lg hover:bg-gray-200 active:bg-gray-300 transition font-semibold text-base sm:text-lg shadow-lg">
                   Registrarse
                </a>
            </div>

        </div>

    </div>

</body>
</html>