document.addEventListener("DOMContentLoaded", () => {
    const modalEl = document.getElementById("modalCrearEjercicio");

    // Evitamos crear listeners duplicados
    if (window.wodModalListeners) return;
    window.wodModalListeners = true;

    // Si el modal no existe, no hacemos nada
    if (!modalEl) return;

    // Bootstrap debe estar en window.bootstrap
    const modal = new window.bootstrap.Modal(modalEl);

    // Evento desde Livewire → mostrar modal
    window.addEventListener("show-bs-modal", () => {
        modal.show();
    });

    // Evento desde Livewire → ocultar modal
    window.addEventListener("hide-bs-modal", () => {
        modal.hide();
    });

    // Evento propio de Bootstrap → cuando termina de cerrarse
    modalEl.addEventListener("hidden.bs.modal", () => {
        Livewire.dispatch("reset-modal-state");
    });
});
