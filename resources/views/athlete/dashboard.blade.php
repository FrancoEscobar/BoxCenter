@extends('layouts.app')

@section('content')
{{-- Contenedor de notificaciones --}}
<div x-data="notificationHandler()" x-init="initEcho()" class="fixed top-20 right-4 z-50 space-y-2">
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="notification.show" 
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg max-w-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">Clase Cancelada</p>
                    <p class="mt-1 text-sm" x-text="notification.mensaje"></p>
                </div>
                <button @click="removeNotification(notification.id)" class="ml-4 flex-shrink-0">
                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

{{-- Banner de próxima clase (componente Livewire) --}}
<livewire:athlete.next-class-banner/>

<div>
    {{-- Clases disponibles --}}
    <livewire:athlete.avaliable-classes/>
</div>

{{-- Estilos globales --}}
<style>
    .hover-shadow {
        transition: all 0.25s ease-in-out;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
    }

    .card:active {
        transform: scale(0.98);
        transition: 0.1s;
    }
</style>

<script>
function notificationHandler() {
    return {
        notifications: [],
        nextId: 1,

        initEcho() {
            if (typeof Echo === 'undefined') {
                console.error('Laravel Echo no está configurado');
                return;
            }

            const userId = {{ auth()->id() }};
            
            // Escuchar notificaciones en el canal privado del usuario
            Echo.private(`user.${userId}`)
                .listen('.clase.cancelada', (data) => {
                    console.log('Notificación recibida:', data);
                    this.addNotification(data.mensaje);
                    
                    // Refrescar los componentes Livewire
                    Livewire.dispatch('reserva-actualizada');
                });
        },

        addNotification(mensaje) {
            const id = this.nextId++;
            this.notifications.push({
                id: id,
                mensaje: mensaje,
                show: true
            });

            // Auto-ocultar después de 10 segundos
            setTimeout(() => {
                this.removeNotification(id);
            }, 10000);
        },

        removeNotification(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index !== -1) {
                this.notifications[index].show = false;
                setTimeout(() => {
                    this.notifications.splice(index, 1);
                }, 300); // Esperar a que termine la animación
            }
        }
    }
}
</script>

@endsection

