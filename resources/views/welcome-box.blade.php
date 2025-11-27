<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoxCenter</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex flex-col items-center justify-center p-6 text-center">

        <!-- Imagen principal -->
        <img src="{{ asset('images/legion-box.png') }}"  
        class="mx-auto block w-full max-w-md h-auto rounded-xl shadow-lg mb-8">

        <!-- Texto -->
        <h1 class="text-3xl font-bold mb-4">Bienvenido a LEGIÓN BOX</h1>
        <p class="text-gray-700 mb-8">
            Entrená distinto. Superate cada día.  
            Unite a una comunidad que te empuja a ser mejor.
        </p>

        <!-- Botones -->
        <div class="flex gap-4">
            <a href="{{ route('login') }}" 
               class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
               Iniciar sesión
            </a>

            <a href="{{ route('register') }}" 
               class="px-5 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
               Registrarse
            </a>
        </div>

    </div>

</body>
</html>
