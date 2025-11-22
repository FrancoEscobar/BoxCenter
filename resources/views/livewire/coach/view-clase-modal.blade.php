<div class="modal fade" id="viewClaseModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            @if($clase)
            <div class="modal-header {{ $clase->estado === 'cancelada' ? 'bg-danger' : 'bg-secondary' }} text-white">
                <h5 class="modal-title fw-bold">
                    @if($isEditing)
                        <i class="bi bi-pencil-square me-2"></i>Editando Clase
                    @else
                        @if($clase->estado === 'cancelada')
                            <i class="bi bi-x-circle me-2"></i>CLASE CANCELADA
                        @else
                            <i class="bi bi-info-circle me-2"></i>Detalles de Clase
                        @endif
                    @endif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @if($isEditing)
                    <div class="animate__animated animate__fadeIn">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha</label>
                            <div class="input-group" wire:ignore>
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-calendar-event"></i>
                                </span>
                                <input 
                                    type="text" 
                                    class="form-control border-start-0 ps-0 bg-white" 
                                    id="edit-date-input" 
                                    placeholder="dd/mm/aaaa"
                                >
                            </div>
                            @error('edit_fecha') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Inicio</label>
                                <input type="time" class="form-control" wire:model="edit_hora_inicio">
                                @error('edit_hora_inicio') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Fin</label>
                                <input type="time" class="form-control" wire:model="edit_hora_fin">
                                @error('edit_hora_fin') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Coach</label>
                            <select class="form-select" wire:model="edit_coach_id">
                                @foreach($coaches as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('edit_coach_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cupo Total</label>
                            <input type="number" class="form-control" wire:model="edit_cupo" min="1">
                            @error('edit_cupo') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                @else
                    <div class="row mb-4 border-bottom pb-3 position-relative">
                        <div class="col-8">
                            <h4 class="fw-bold text-primary">{{ $clase->tipo_entrenamiento->nombre }}</h4>
                            
                            <p class="mb-1 small text-muted">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $clase->fecha->format('d/m/Y') }}
                            </p>
                            <p class="mb-1 small text-muted">
                                <i class="bi bi-clock me-1"></i>
                                {{ $clase->hora_inicio->format('H:i') }} a {{ $clase->hora_fin->format('H:i') }}
                            </p>
                            <p class="mb-1 small text-muted">
                                <i class="bi bi-person-badge me-1"></i>
                                Coach: <strong>{{ $clase->coach->name }}</strong>
                            </p>
                        </div>

                        <div class="col-4 d-flex flex-column align-items-end justify-content-center pt-4">
                            <button class="btn btn-outline-primary btn-sm px-3" wire:click="toggleUsers">
                                <i class="bi bi-people me-1"></i>
                                {{ $this->inscriptos?->count() ?? 0 }} / {{ $clase->cupo }}
                            </button>
                            <span class="small mt-1 text-{{ ($this->inscriptos?->count() ?? 0) >= $clase->cupo ? 'danger' : 'success' }}">
                                {{ ($this->inscriptos?->count() ?? 0) >= $clase->cupo ? 'Cupo lleno' : 'Disponible' }}
                            </span>
                        </div>
                    </div>

                    @if($showUsers)
                        <div class="alert alert-info py-2 mb-4 animate__animated animate__fadeIn">
                            <h6 class="small fw-bold border-bottom pb-1">Inscriptos:</h6>
                            <ul class="list-unstyled small mb-0">
                                @if($inscriptos && $inscriptos->count() > 0)
                                    @foreach($inscriptos as $asistencia)
                                        <li><i class="bi bi-check-circle-fill me-1 text-success"></i>{{ $asistencia->usuario->name }}</li>
                                    @endforeach
                                @else
                                    <li class="text-muted fst-italic small">Aún no hay alumnos inscriptos.</li>
                                @endif
                            </ul>
                        </div>
                    @endif
                    
                    @if($clase->wod)
                        <h5 class="fw-bold mb-2 pb-1 border-bottom">Rutina: {{ $clase->wod->nombre }}</h5>
                        
                        <div class="small mb-3 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-stopwatch"></i> Duración: <strong>{{ $clase->wod->duracion ?? '-' }} min</strong></span>
                            
                            <a href="{{ route('coach.wods.edit', $clase->wod->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i> Ver WOD
                            </a>
                        </div>

                        <p class="small text-muted border-start border-primary ps-2 mb-3">
                            {{ $clase->wod->descripcion }}
                        </p>

                        <h6 class="fw-bold small mt-2">Ejercicios:</h6>
                        <table class="table table-sm small table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 45%">Ejercicio</th>
                                    <th style="width: 30%">Series x Reps</th>
                                    <th style="width: 20%">Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clase->wod->ejercicios as $ejercicio)
                                    <tr>
                                        <td>{{ $ejercicio->pivot->orden }}</td>
                                        <td>{{ $ejercicio->nombre }}</td>
                                        <td><strong>{{ $ejercicio->pivot->series }}</strong> x <strong>{{ $ejercicio->pivot->repeticiones }}</strong></td>
                                        <td>{{ $ejercicio->pivot->duracion ? $ejercicio->pivot->duracion.'s' : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                         <div class="alert alert-warning small border-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i> No hay WOD asignado a esta clase.
                         </div>
                    @endif
                @endif

            </div>

            <div class="modal-footer bg-light">
                
                @if($isEditing)
                    <button type="button" class="btn btn-secondary" wire:click="cancelEditing">Cancelar</button>
                    <button type="button" class="btn btn-primary px-4" wire:click="updateClase">
                        <span wire:loading.remove wire:target="updateClase">Guardar Cambios</span>
                        <span wire:loading wire:target="updateClase">
                            <span class="spinner-border spinner-border-sm" role="status"></span> Guardando...
                        </span>
                    </button>
                @else
                    <div class="me-auto">
                        @if($clase->estado === 'cancelada')
                            <button class="btn btn-success text-white btn-sm" 
                                onclick="confirm('¿Estás seguro de reactivar esta clase?') || event.stopImmediatePropagation()"
                                wire:click="toggleEstadoClase">
                                <i class="bi bi-check-circle me-1"></i> Habilitar Clase
                            </button>
                        @else
                            <button class="btn btn-outline-danger btn-sm" 
                                onclick="confirm('¿Estás seguro de cancelar esta clase? Los alumnos inscriptos serán notificados.') || event.stopImmediatePropagation()"
                                wire:click="toggleEstadoClase">
                                <i class=""></i> Cancelar Clase
                            </button>
                        @endif
                    </div>

                    @if($clase->estado !== 'cancelada')
                        <button type="button" class="btn btn-primary" wire:click="startEditing">
                            <i class="bi bi-pencil-square me-1"></i> Editar
                        </button>
                    @endif

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                @endif
            </div>
            
            @else
                <div class="p-5 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
    
    document.addEventListener('livewire:init', () => {
        //Mostrar el Modal al recibir el evento
        Livewire.on('show-view-modal', () => {
            const modalEl = document.getElementById('viewClaseModal');
            if (modalEl) {
                const bsModal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                bsModal.show();
            }
        });

        //Inicializar Flatpickr al Editar
        Livewire.on('initialize-edit-date', (event) => {
            // Timeout para asegurar que el HTML del input ya se renderizó
            setTimeout(() => {
                const input = document.getElementById('edit-date-input');
                
                if (input) {
                    flatpickr(input, {
                        locale: "es",
                        dateFormat: "Y-m-d", // Formato interno (Backend)
                        altInput: true,      // Formato visual
                        altFormat: "d/m/Y",  // Lo que el usuario ve (24/11/2025)
                        allowInput: true,
                        defaultDate: event.date || event, // Pre-cargar la fecha actual
                        
                        onChange: function(selectedDates, dateStr) {
                            // Sincronizar con Livewire
                            @this.set('edit_fecha', dateStr);
                        }
                    });
                }
            }, 100); // 100ms de retardo
        });

    });
</script>