<x-guest-layout>
    <div class="mb-6 text-sm sm:text-base text-gray-600 leading-relaxed">
        {{ __('¿Olvidaste tu contraseña? No hay problema. Solo decinos tu dirección de correo electrónico y te enviaremos un enlace para restablecerla, que te permitirá elegir una nueva.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
            <a 
                href="{{ route('login') }}" 
                class="text-sm text-blue-600 hover:text-blue-700 underline"
            >
                Volver al inicio de sesión
            </a>

            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Enviar enlace de restablecimiento') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>