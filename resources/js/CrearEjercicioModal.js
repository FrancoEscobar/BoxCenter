// resources/js/wod-events.js

document.addEventListener('livewire:init', () => {
    
    // Función para obtener Bootstrap de forma segura
    const getBootstrap = () => {
        return window.bootstrap || undefined;
    };

    // 1. Listener para ABRIR el modal
    Livewire.on('show-bs-modal', () => {
        const modalEl = document.getElementById('modalCrearEjercicio');
        const bs = getBootstrap();

        if (modalEl && bs) {
            // getOrCreateInstance es vital para recuperar el modal si Livewire refrescó el DOM
            const modal = bs.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            console.error('Error: No se encontró el modal o Bootstrap no está cargado.');
        }
    });

    // 2. Listener para CERRAR el modal
    Livewire.on('hide-bs-modal', () => {
        const modalEl = document.getElementById('modalCrearEjercicio');
        const bs = getBootstrap();

        if (modalEl && bs) {
            const modal = bs.Modal.getOrCreateInstance(modalEl);
            modal.hide();
        }
    });

    // 3. Listener para LIMPIAR (Delegación de eventos)
    // Usamos delegación en el body porque si Livewire redibuja el modal, 
    // un addEventListener directo al elemento se perdería.
    document.body.addEventListener('hidden.bs.modal', (event) => {
        if (event.target.id === 'modalCrearEjercicio') {
            Livewire.dispatch('reset-modal-state');
        }
    });
});