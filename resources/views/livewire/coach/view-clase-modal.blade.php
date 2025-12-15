<div wire:ignore.self>
    @if($mostrarModal)
    <div class="modal-overlay" @click.self="if (@js($isEditing || $editandoWod)) { if(confirm('¿Cerrar sin guardar los cambios?')) { $wire.cerrarSinGuardar(); } } else { $wire.cerrarModal(); }">
        <div class="modal-content">
            @if($clase)
                <div class="modal-header">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $clase->tipo_entrenamiento->nombre }}</h5>
                        @if($clase->estado === 'cancelada')
                            <small class="text-danger mt-1 d-block"><i class="bi bi-x-circle-fill me-1"></i>Clase Cancelada</small>
                        @endif
                    </div>
                    <button 
                        @if($isEditing || $editandoWod)
                            onclick="if(confirm('¿Cerrar sin guardar los cambios?')) { @this.call('cerrarSinGuardar'); }"
                        @else
                            wire:click="cerrarModal"
                        @endif
                        class="btn-close-modal"><i class="bi bi-x-lg"></i></button>
                </div>

                <div class="modal-body">
                        @if($isEditing)
                            <form wire:submit.prevent="updateClase" class="mb-3">
                                <div class="mb-2">
                                    <label class="form-label">Fecha (DD/MM/AAAA)</label>
                                    <input type="text" 
                                           wire:model.defer="edit_fecha_display" 
                                           class="form-control form-control-sm" 
                                           placeholder="DD/MM/AAAA"
                                           maxlength="10"
                                           onkeypress="return event.charCode >= 47 && event.charCode <= 57"
                                           oninput="
                                               let val = this.value.replace(/[^0-9]/g, '');
                                               if (val.length >= 2) {
                                                   val = val.substring(0, 2) + '/' + val.substring(2);
                                               }
                                               if (val.length >= 5) {
                                                   val = val.substring(0, 5) + '/' + val.substring(5, 9);
                                               }
                                               this.value = val;
                                           "
                                           required>
                                    @error('edit_fecha') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @error('edit_fecha_display') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Hora inicio</label>
                                    <div class="d-flex gap-1 align-items-center">
                                        <input type="text" 
                                               wire:model.defer="edit_hora_inicio_hora" 
                                               class="form-control form-control-sm text-center" 
                                               style="width: 60px;"
                                               placeholder="HH"
                                               maxlength="2"
                                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                               oninput="
                                                   this.value = this.value.replace(/[^0-9]/g, '');
                                                   if(this.value.length === 2 && parseInt(this.value) > 23) this.value = '23';
                                               "
                                               required>
                                        <span class="fw-bold">:</span>
                                        <input type="text" 
                                               wire:model.defer="edit_hora_inicio_minuto" 
                                               class="form-control form-control-sm text-center" 
                                               style="width: 60px;"
                                               placeholder="MM"
                                               maxlength="2"
                                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                               oninput="
                                                   this.value = this.value.replace(/[^0-9]/g, '');
                                                   if(this.value.length === 2 && parseInt(this.value) > 59) this.value = '59';
                                               "
                                               required>
                                    </div>
                                    @error('edit_hora_inicio') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @error('edit_hora_inicio_hora') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @error('edit_hora_inicio_minuto') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Hora fin</label>
                                    <div class="d-flex gap-1 align-items-center">
                                        <input type="text" 
                                               wire:model.defer="edit_hora_fin_hora" 
                                               class="form-control form-control-sm text-center" 
                                               style="width: 60px;"
                                               placeholder="HH"
                                               maxlength="2"
                                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                               oninput="
                                                   this.value = this.value.replace(/[^0-9]/g, '');
                                                   if(this.value.length === 2 && parseInt(this.value) > 23) this.value = '23';
                                               "
                                               required>
                                        <span class="fw-bold">:</span>
                                        <input type="text" 
                                               wire:model.defer="edit_hora_fin_minuto" 
                                               class="form-control form-control-sm text-center" 
                                               style="width: 60px;"
                                               placeholder="MM"
                                               maxlength="2"
                                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                               oninput="
                                                   this.value = this.value.replace(/[^0-9]/g, '');
                                                   if(this.value.length === 2 && parseInt(this.value) > 59) this.value = '59';
                                               "
                                               required>
                                    </div>
                                    @error('edit_hora_fin') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @error('edit_hora_fin_hora') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @error('edit_hora_fin_minuto') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tipo de Entrenamiento</label>
                                    <select wire:model.defer="edit_tipo_entrenamiento_id" class="form-select form-select-sm" required>
                                        <option value="" disabled>Seleccionar...</option>
                                        @foreach($tipos_entrenamiento as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('edit_tipo_entrenamiento_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Coach</label>
                                    <select wire:model.defer="edit_coach_id" class="form-select form-select-sm" required>
                                        <option value="" disabled>Seleccionar</option>
                                        @foreach($coaches as $coach)
                                            <option value="{{ $coach->id }}">{{ $coach->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('edit_coach_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Cupo</label>
                                    <input type="number" wire:model.defer="edit_cupo" class="form-control form-control-sm" min="1" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                                </div>
                                <div class="d-flex gap-2 justify-content-end mt-3">
                                    <button type="button" wire:click="cancelEditing" class="btn btn-secondary btn-sm">Cancelar</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                                </div>
                            </form>
                        @else
                            <div class="detail-item">
                                <i class="bi bi-calendar-event text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Fecha</small>
                                    <strong>{{ \Carbon\Carbon::parse($clase->fecha)->translatedFormat('l j \d\e F') }}</strong>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="bi bi-clock text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Horario</small>
                                    <strong>{{ $clase->hora_inicio->format('H:i') }} - {{ $clase->hora_fin->format('H:i') }}</strong>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="bi bi-person-badge text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Coach</small>
                                    <strong>{{ $clase->coach->name }}</strong>
                                </div>
                            </div>

                            <div class="detail-item">
                                <i class="bi bi-people text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Inscriptos</small>
                                    <strong>{{ $this->inscriptos?->count() ?? 0 }} de {{ $clase->cupo }}</strong>
                                    <div class="d-flex justify-content-end mt-1">
                                        <button wire:click="toggleUsers" type="button" class="inline-flex items-center gap-2 px-3 py-1 rounded-2xl text-sm font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 transition border-0" style="height: 28px; min-width: 0;">
                                            <i class="bi bi-eye" style="font-size: 1.1em;"></i>
                                            <span>{{ $showUsers ? 'Ocultar lista' : 'Ver inscriptos' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @if($showUsers)
                        <div class="alert alert-info py-2 mt-2 mb-2 animate__animated animate__fadeIn">
                            <h6 class="small fw-bold border-bottom pb-1 mb-2">
                                Inscriptos:
                                @if($desdeHistorial)
                                    <small class="text-muted fw-normal">(Tomar asistencia)</small>
                                @endif
                            </h6>
                            
                            @if($inscriptos && $inscriptos->count() > 0)
                                @if($desdeHistorial)
                                    {{-- Vista con controles de asistencia para historial --}}
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($inscriptos as $asistencia)
                                            <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($asistencia->estado === 'asistio')
                                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 1.2rem;"></i>
                                                    @elseif($asistencia->estado === 'ausente')
                                                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 1.2rem;"></i>
                                                    @else
                                                        <i class="bi bi-question-circle-fill text-warning" style="font-size: 1.2rem;"></i>
                                                    @endif
                                                    <span class="small fw-semibold">{{ $asistencia->usuario->name }}</span>
                                                </div>
                                                
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button 
                                                        type="button" 
                                                        wire:click="marcarAsistencia({{ $asistencia->id }}, 'asistio')"
                                                        class="btn {{ $asistencia->estado === 'asistio' ? 'btn-success' : 'btn-outline-success' }}"
                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button 
                                                        type="button" 
                                                        wire:click="marcarAsistencia({{ $asistencia->id }}, 'ausente')"
                                                        class="btn {{ $asistencia->estado === 'ausente' ? 'btn-danger' : 'btn-outline-danger' }}"
                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Vista simple para clases futuras --}}
                                    <ul class="list-unstyled small mb-0">
                                        @foreach($inscriptos as $asistencia)
                                            <li><i class="bi bi-check-circle-fill me-1 text-success"></i>{{ $asistencia->usuario->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            @else
                                <p class="text-muted fst-italic small mb-0">Aún no hay alumnos inscriptos.</p>
                            @endif
                        </div>
                    @endif

                    @if($clase->wod || $isEditing)
                        <div class="border-top pt-3 mt-3">
                            @if($editandoWod)
                                {{-- Modo edición de WOD --}}
                                <div class="mb-3">
                                    <h6 class="fw-bold mb-3"><i class="bi bi-pencil text-primary me-2"></i>Editar WOD</h6>
                                    
                                    <div class="mb-2">
                                        <label class="form-label small">Nombre del WOD</label>
                                        <input type="text" wire:model="edit_wod_nombre" class="form-control form-control-sm" required>
                                        @error('edit_wod_nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label small">Duración (minutos)</label>
                                        <input type="number" wire:model="edit_wod_duracion" class="form-control form-control-sm" min="1">
                                        @error('edit_wod_duracion') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label small">Descripción</label>
                                        <textarea wire:model="edit_wod_descripcion" class="form-control form-control-sm" rows="2"></textarea>
                                        @error('edit_wod_descripcion') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Ejercicios --}}
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label small fw-bold mb-0">Ejercicios</label>
                                        <div class="d-flex gap-2">
                                            <button type="button" wire:click="abrirModalCrearEjercicio" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-plus-square"></i> Crear
                                            </button>
                                            <button type="button" wire:click="agregarEjercicioWod" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-plus-circle"></i> Agregar
                                            </button>
                                        </div>
                                    </div>

                                    @foreach ($ejerciciosWod as $i => $e)
                                        @if($editingExerciseIndex === $i)
                                            {{-- Modo edición: formato wod-manager --}}
                                            <div class="card p-2 p-sm-3 mb-2 mb-sm-3">
                                                <div class="row g-2">
                                                    <input type="hidden" name="ejercicios[{{ $i }}][orden]" value="{{ $i + 1 }}">

                                                    <!-- SELECT EJERCICIO -->
                                                    <div class="col-12 col-md-4">
                                                        <label class="small">Ejercicio</label>
                                                        <select class="form-select form-select-sm" wire:model="ejerciciosWod.{{ $i }}.id" required>
                                                            <option value="">Seleccionar…</option>
                                                            @foreach ($listaEjercicios as $opt)
                                                                <option value="{{ $opt->id }}">{{ $opt->nombre }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <!-- INPUT SERIES -->
                                                    <div class="col-4 col-md-2">
                                                        <label class="small">Series</label>
                                                        <input type="number" class="form-control form-control-sm" wire:model="ejerciciosWod.{{ $i }}.series" min="1" required>
                                                    </div>

                                                    <!-- INPUT REPETICIONES -->
                                                    <div class="col-4 col-md-2">
                                                        <label class="small">Reps</label>
                                                        <input type="number" class="form-control form-control-sm" wire:model="ejerciciosWod.{{ $i }}.repeticiones" min="1" required>
                                                    </div>

                                                    <!-- INPUT DURACIÓN OPCIONAL -->
                                                    <div class="col-4 col-md-2">
                                                        <label class="small">Tiempo</label>
                                                        <input type="number" class="form-control form-control-sm" wire:model="ejerciciosWod.{{ $i }}.duracion" min="1" placeholder="Opcional">
                                                    </div>

                                                    <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                                                        <button type="button" class="btn btn-success btn-sm flex-fill" wire:click="guardarEjercicioWod({{ $i }})">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm flex-fill" wire:click="eliminarEjercicioWod({{ $i }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Vista normal: banner blanco read-only --}}
                                            <div class="card mb-2 border shadow-sm" style="border-radius: 10px;">
                                                <div class="p-2 p-sm-3 bg-white">
                                                    {{-- Layout móvil: stacked --}}
                                                    <div class="d-flex d-md-none flex-column gap-2">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="d-flex flex-column gap-1">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary p-0" wire:click="moverEjercicioArriba({{ $i }})" style="width: 24px; height: 20px; line-height: 1;" @if($i === 0) disabled @endif>
                                                                    <i class="bi bi-chevron-up" style="font-size: 0.7rem;"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary p-0" wire:click="moverEjercicioAbajo({{ $i }})" style="width: 24px; height: 20px; line-height: 1;" @if($i === count($ejerciciosWod) - 1) disabled @endif>
                                                                    <i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
                                                                </button>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <span class="fw-semibold small">
                                                                    @php
                                                                        $ejercicio = $listaEjercicios->firstWhere('id', $e['id']);
                                                                    @endphp
                                                                    {{ $ejercicio ? $ejercicio->nombre : 'Seleccionar ejercicio…' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center gap-2 text-muted small">
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <strong>{{ $e['series'] ?? '-' }}</strong>
                                                                    <span>×</span>
                                                                    <strong>{{ $e['repeticiones'] ?? '-' }}</strong>
                                                                </div>
                                                                @if(!empty($e['duracion']))
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <i class="bi bi-stopwatch"></i>
                                                                        <strong>{{ $e['duracion'] }}</strong>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="d-flex gap-1">
                                                                <button type="button" class="btn btn-sm btn-primary px-2 py-1" wire:click="editarEjercicioWod({{ $i }})" style="border-radius: 6px; font-size: 0.875rem;">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-danger px-2 py-1" wire:click="eliminarEjercicioWod({{ $i }})" style="border-radius: 6px; font-size: 0.875rem;">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Layout desktop: horizontal --}}
                                                    <div class="d-none d-md-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                                                            <div class="d-flex flex-column gap-1">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary p-0" wire:click="moverEjercicioArriba({{ $i }})" style="width: 32px; height: 24px; line-height: 1;" @if($i === 0) disabled @endif>
                                                                    <i class="bi bi-chevron-up"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary p-0" wire:click="moverEjercicioAbajo({{ $i }})" style="width: 32px; height: 24px; line-height: 1;" @if($i === count($ejerciciosWod) - 1) disabled @endif>
                                                                    <i class="bi bi-chevron-down"></i>
                                                                </button>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <span class="fw-semibold">
                                                                    @php
                                                                        $ejercicio = $listaEjercicios->firstWhere('id', $e['id']);
                                                                    @endphp
                                                                    {{ $ejercicio ? $ejercicio->nombre : 'Seleccionar ejercicio…' }}
                                                                </span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-3 text-muted">
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <strong>{{ $e['series'] ?? '-' }}</strong>
                                                                    <span>series</span>
                                                                </div>
                                                                <span>×</span>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <strong>{{ $e['repeticiones'] ?? '-' }}</strong>
                                                                    <span>reps</span>
                                                                </div>
                                                                @if(!empty($e['duracion']))
                                                                    <div class="d-flex align-items-center gap-1 ms-2">
                                                                        <i class="bi bi-stopwatch"></i>
                                                                        <strong>{{ $e['duracion'] }}</strong>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 ms-3">
                                                            <button type="button" class="btn btn-sm btn-primary" wire:click="editarEjercicioWod({{ $i }})" style="border-radius: 8px;">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger" wire:click="eliminarEjercicioWod({{ $i }})" style="border-radius: 8px;">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    <div class="d-flex gap-2 justify-content-end mt-3">
                                        <button type="button" wire:click="cancelarEdicionWod" class="btn btn-secondary btn-sm">Cancelar</button>
                                        <button type="button" wire:click="guardarWod" class="btn btn-primary btn-sm">Guardar WOD</button>
                                    </div>
                                </div>
                            @elseif($isEditing)
                                {{-- Dropdown para seleccionar WOD en modo edición de clase --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                                        <label class="form-label fw-bold mb-0"><i class="bi bi-card-list text-primary me-2"></i>Seleccionar WOD</label>
                                        <div class="d-flex gap-1">
                                            <button type="button" wire:click="editarWod" class="btn btn-outline-primary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" wire:click="crearNuevoWod" class="btn btn-outline-success" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                <i class="bi bi-plus-circle"></i> Crear
                                            </button>
                                        </div>
                                    </div>
                                    <select wire:model.live="selectedWodId" class="form-select form-select-sm">
                                        <option value="">Sin WOD asignado</option>
                                        @foreach($wods as $wodOption)
                                            <option value="{{ $wodOption->id }}">{{ $wodOption->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($wod)
                                    <small class="text-muted d-block mb-2"><i class="bi bi-stopwatch me-1"></i>Duración: <strong>{{ $wod->duracion ?? '-' }} min</strong></small>
                                    <p class="small text-muted border-start border-primary ps-2 mb-3">{{ $wod->descripcion }}</p>

                                    @if($wod->ejercicios && $wod->ejercicios->count() > 0)
                                        <h6 class="fw-bold small mt-2 mb-2">Ejercicios:</h6>
                                        <div class="table-responsive">
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
                                                    @foreach ($wod->ejercicios as $ejercicio)
                                                        <tr>
                                                            <td>{{ $ejercicio->pivot->orden }}</td>
                                                            <td>{{ $ejercicio->nombre }}</td>
                                                            <td><strong>{{ $ejercicio->pivot->series }}</strong> x <strong>{{ $ejercicio->pivot->repeticiones }}</strong></td>
                                                            <td>{{ $ejercicio->pivot->duracion ? $ejercicio->pivot->duracion.'s' : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endif
                            @else
                                @if($clase->wod)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-card-list text-primary me-2"></i>Rutina: {{ $clase->wod->nombre }}</h6>
                                </div>
                                <small class="text-muted d-block mb-2"><i class="bi bi-stopwatch me-1"></i>Duración: <strong>{{ $clase->wod->duracion ?? '-' }} min</strong></small>
                                <p class="small text-muted border-start border-primary ps-2 mb-3">{{ $clase->wod->descripcion }}</p>

                                @if($clase->wod->ejercicios && $clase->wod->ejercicios->count() > 0)
                                    <h6 class="fw-bold small mt-2 mb-2">Ejercicios:</h6>
                                    <div class="table-responsive">
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
                                    </div>
                                @endif
                                @endif
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning small border-warning mt-3"><i class="bi bi-exclamation-triangle me-1"></i> No hay WOD asignado a esta clase.</div>
                    @endif
                </div>

                @if(!$isEditing)
                <div class="modal-footer">
                    @if(!$desdeHistorial)
                        @if($clase->estado === 'cancelada')
                            <button class="btn btn-success text-white btn-sm" onclick="confirm('¿Estás seguro de reactivar esta clase?') || event.stopImmediatePropagation()" wire:click="toggleEstadoClase"><i class="bi bi-check-circle me-1"></i> Habilitar</button>
                            <button class="btn btn-danger text-white btn-sm" wire:click="deleteClase" wire:confirm="¿Estás seguro de eliminar esta clase DEFINITIVAMENTE? Esta acción no se puede deshacer."><i class="bi bi-trash-fill me-1"></i> Eliminar</button>
                        @else
                            <button class="btn btn-outline-danger btn-sm" onclick="confirm('¿Estás seguro de cancelar esta clase? Los alumnos inscriptos serán notificados.') || event.stopImmediatePropagation()" wire:click="toggleEstadoClase"><i class="bi bi-x-circle me-1"></i> Cancelar Clase</button>
                            <button type="button" class="btn btn-primary btn-sm" wire:click="startEditing"><i class="bi bi-pencil-square me-1"></i> Editar</button>
                        @endif
                    @endif
                </div>
                @endif
            @else
                <div class="p-5 text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 1050; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .modal-content { background: white; border-radius: 1rem; max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .modal-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 1.25rem; border-bottom: 1px solid #e9ecef; gap: 1rem; }
        .btn-close-modal { border: none; background: #f0f2f5; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s; flex-shrink: 0; }
        .btn-close-modal:hover { background: #e4e6eb; }
        .modal-body { padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
        .detail-item { display: flex; align-items: flex-start; gap: 0.75rem; }
        .detail-item i { font-size: 1.25rem; margin-top: 0.25rem; flex-shrink: 0; }
        .modal-footer { padding: 1.25rem; border-top: 1px solid #e9ecef; display: flex; gap: 1rem; justify-content: flex-end; flex-wrap: wrap; }
        .alert { border-radius: 0.5rem; border: 1px solid; }
        .table { margin-bottom: 0; }
</style>

    <script>
        document.addEventListener('livewire:init', () => {
            document.addEventListener('input', (e) => {
                if (e.target.classList.contains('time-input')) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    
                    // Limitar horas a 23
                    if (value.length >= 1) {
                        let firstDigit = parseInt(value[0]);
                        if (firstDigit > 2) {
                            value = '0' + value;
                        }
                    }
                    
                    if (value.length >= 2) {
                        let hours = parseInt(value.substring(0, 2));
                        if (hours > 23) {
                            value = '23' + value.substring(2);
                        }
                    }
                    
                    // Insertar dos puntos
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + ':' + value.substring(2, 4);
                    }
                    
                    // Limitar minutos a 59
                    if (value.length >= 4) {
                        let minutes = parseInt(value.substring(3, 5));
                        if (minutes > 59) {
                            value = value.substring(0, 3) + '59';
                        }
                    }
                    
                    e.target.value = value;
        });
    </script>

    {{-- Modal para crear ejercicio --}}
    @if($mostrarModalCrearEjercicio)
    <div class="modal-overlay" style="z-index: 1060;" @click.self="$wire.cerrarModalCrearEjercicio()">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h6 class="fw-bold mb-0">Crear Nuevo Ejercicio</h6>
                <button wire:click="cerrarModalCrearEjercicio" class="btn-close-modal"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nombre del Ejercicio *</label>
                    <input type="text" wire:model="nuevo_ejercicio_nombre" class="form-control form-control-sm" placeholder="Ej: Sentadillas" autofocus>
                    @error('nuevo_ejercicio_nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Descripción (Opcional)</label>
                    <textarea wire:model="nuevo_ejercicio_descripcion" class="form-control form-control-sm" rows="3" placeholder="Descripción del ejercicio..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click="cerrarModalCrearEjercicio" class="btn btn-secondary btn-sm">Cancelar</button>
                <button type="button" wire:click="crearEjercicio" class="btn btn-primary btn-sm">Crear Ejercicio</button>
            </div>
        </div>
    </div>
    @endif

@endif
</div>
