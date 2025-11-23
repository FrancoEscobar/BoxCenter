<div class="modal fade" id="createClaseModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Clase
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Fecha</label>
                    <div class="input-group" wire:ignore>
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-event"></i>
                        </span>
                        <input 
                            type="text" 
                            class="form-control border-start-0 ps-0 bg-white" 
                            id="modal-date-input" 
                            placeholder="dd/mm/aaaa"
                        >
                    </div>
                    @error('fecha') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Inicio</label>
                        <input type="time" class="form-control" wire:model.change="hora_inicio">
                        @error('hora_inicio') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Fin</label>
                        <input type="time" class="form-control" wire:model.change="hora_fin">
                        @error('hora_fin') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Entrenamiento</label>
                    <select class="form-select" 
                            wire:model.change="tipo_entrenamiento_id"
                            wire:key="select-tipo-{{ $tipo_entrenamiento_id }}"
                    >
                        <option value="" disabled>Seleccionar...</option>
                        @foreach($tipos_entrenamiento as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('tipo_entrenamiento_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Coach a cargo</label>
                    <select class="form-select" 
                            wire:model.change="coach_id"
                            wire:key="select-coach-{{ $coach_id }}"
                    >
                        <option value="" disabled>Seleccionar...</option>
                        @foreach($coaches as $coach)
                            <option value="{{ $coach->id }}">{{ $coach->name }}</option>
                        @endforeach
                    </select>
                    @error('coach_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3 p-3 bg-light rounded border border-light">
                    <label class="form-label fw-bold text-primary">
                        <i class="bi bi-card-list me-1"></i> Rutina del día (WOD)
                    </label>
                    
                    <div class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <select class="form-select" 
                                    wire:model.live="selected_wod_id"
                                    wire:key="select-wod-{{ $selected_wod_id }}"
                            >
                                <option value="" disabled>Seleccionar WOD existente...</option>
                                @foreach($wods_disponibles as $wod)
                                    <option value="{{ $wod->id }}">{{ $wod->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="btn btn-outline-secondary" wire:click="cargarWods" title="Refrescar lista">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    @error('selected_wod_id') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror

                    <div class="mt-2 text-end">
                        <span class="text-muted small me-1">¿No está en la lista?</span>
                        <a href="#" target="_blank" class="text-decoration-none fw-bold small">
                            <i class="bi bi-plus-circle"></i> Crear nuevo WOD
                        </a>
                    </div>

                    @if($selected_wod_id)
                        <div class="small text-muted mt-2 p-2 bg-white rounded border animate__animated animate__fadeIn">
                            <strong>Descripción:</strong><br>
                            {{ Str::limit($wods_disponibles->find($selected_wod_id)?->descripcion ?? '', 100) }}
                        </div>
                    @endif
                </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    
                    <button 
                        type="button" 
                        class="btn btn-primary px-4"
                        onclick="confirm('¿Estás seguro de que deseas crear esta clase?') && @this.save()"
                    >
                        <span wire:loading.remove wire:target="save">Crear Clase</span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Guardando...
                        </span>
                    </button>
                </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // Inicializamos Flatpickr en el input del modal
    const modalPicker = flatpickr("#modal-date-input", {
        locale: "es",
        dateFormat: "Y-m-d", // Formato interno (para la base de datos)
        altInput: true,      // Activa el input "máscara"
        altFormat: "d/m/Y",  // Formato visual (dd/mm/aaaa)
        allowInput: true,
        
        // Cuando el usuario elige una fecha...
        onChange: function(selectedDates, dateStr, instance) {
            // ... actualizamos la variable $fecha en Livewire manualmente
            @this.set('fecha', dateStr);
        }
    });

    // Escuchamos al PHP: Cuando se abre el modal con una fecha pre-cargada
    Livewire.on('update-modal-date', (event) => {
        let dateToSet = event.date || event; 
        
        // Actualizamos el calendario visualmente
        modalPicker.setDate(dateToSet);
    });

});
</script>