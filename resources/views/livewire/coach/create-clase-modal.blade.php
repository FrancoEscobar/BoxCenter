<div>
    @if($mostrarModal)
    <div class="modal-overlay" @click.self="$wire.cerrarModal()">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>Nueva Clase</h6>
                <button wire:click="cerrarModal" class="btn-close-modal"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="modal-body">
                <form wire:submit.prevent="save">
                    <div class="mb-2">
                        <label class="form-label">Fecha (DD/MM/AAAA)</label>
                        <input type="text" 
                               wire:model.defer="fecha_display" 
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
                        @error('fecha') <span class="text-danger small">{{ $message }}</span> @enderror
                        @error('fecha_display') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Hora inicio</label>
                        <div class="d-flex gap-1 align-items-center">
                            <input type="text" 
                                   wire:model.defer="hora_inicio_hora" 
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
                                   wire:model.defer="hora_inicio_minuto" 
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
                        @error('hora_inicio') <span class="text-danger small">{{ $message }}</span> @enderror
                        @error('hora_inicio_hora') <span class="text-danger small">{{ $message }}</span> @enderror
                        @error('hora_inicio_minuto') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Hora fin</label>
                        <div class="d-flex gap-1 align-items-center">
                            <input type="text" 
                                   wire:model.defer="hora_fin_hora" 
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
                                   wire:model.defer="hora_fin_minuto" 
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
                        @error('hora_fin') <span class="text-danger small">{{ $message }}</span> @enderror
                        @error('hora_fin_hora') <span class="text-danger small">{{ $message }}</span> @enderror
                        @error('hora_fin_minuto') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Tipo de Entrenamiento</label>
                        <select wire:model.defer="tipo_entrenamiento_id" class="form-select form-select-sm" required>
                            <option value="" disabled selected>Seleccionar...</option>
                            @foreach($tipos_entrenamiento as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_entrenamiento_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Coach</label>
                        <select wire:model.defer="coach_id" class="form-select form-select-sm" required>
                            <option value="" disabled selected>Seleccionar...</option>
                            @foreach($coaches as $coach)
                                <option value="{{ $coach->id }}">{{ $coach->name }}</option>
                            @endforeach
                        </select>
                        @error('coach_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Cupo</label>
                        <input type="number" wire:model.defer="cupo" class="form-control form-control-sm" min="1" required>
                        @error('cupo') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="border-top pt-3 mt-3">
                        @if(!$editandoWod)
                            {{-- Selector de WOD --}}
                            <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <label class="form-label fw-bold mb-0"><i class="bi bi-card-list text-primary me-2"></i>Seleccionar WOD</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if($selectedWodId)
                                        <button type="button" wire:click="editarWod" class="btn btn-outline-primary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                    @endif
                                    <button type="button" wire:click="crearNuevoWod" class="btn btn-outline-success btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                        <i class="bi bi-plus-circle"></i> Crear
                                    </button>
                                </div>
                            </div>
                            <select wire:model.live="selectedWodId" class="form-select form-select-sm" required>
                                <option value="">Sin WOD asignado</option>
                                @foreach($wods_disponibles as $wodOption)
                                    <option value="{{ $wodOption->id }}">{{ $wodOption->nombre }}</option>
                                @endforeach
                            </select>
                            @error('selectedWodId') <span class="text-danger small">{{ $message }}</span> @enderror
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
                            {{-- WOD editing mode --}}
                        <div class="border-top pt-3 mt-3">
                            <div class="mb-3">
                                <h6 class="fw-bold mb-3"><i class="bi bi-pencil text-primary me-2"></i>Editar WOD</h6>
                                
                                <div class="mb-2">
                                    <label class="form-label small">Nombre del WOD</label>
                                    <input type="text" wire:model="edit_wod_nombre" class="form-control form-control-sm" required>
                                    @error('edit_wod_nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label small">Duración (minutos)</label>
                                    <input type="number" wire:model="edit_wod_duracion" class="form-control form-control-sm" min="1" step="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
                                        {{-- Edit mode --}}
                                        <div class="card p-2 p-sm-3 mb-2 mb-sm-3">
                                            <div class="row g-2">
                                                <input type="hidden" name="ejercicios[{{ $i }}][orden]" value="{{ $i + 1 }}">

                                                <div class="col-12 col-md-4">
                                                    <label class="small">Ejercicio</label>
                                                    <select class="form-select form-select-sm" wire:model="ejerciciosWod.{{ $i }}.id" required>
                                                        <option value="">Seleccionar…</option>
                                                        @foreach ($listaEjercicios as $opt)
                                                            <option value="{{ $opt->id }}">{{ $opt->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-4 col-md-2">
                                                    <label class="small">Series</label>
                                                    <input type="number" class="form-control form-control-sm" wire:model="ejerciciosWod.{{ $i }}.series" min="1" step="1" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                </div>

                                                <div class="col-4 col-md-2">
                                                    <label class="small">Reps</label>
                                                    <input type="number" class="form-control form-control-sm" wire:model="ejerciciosWod.{{ $i }}.repeticiones" min="1" step="1" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                </div>

                                                <div class="col-4 col-md-2">
                                                    <label class="small">Tiempo</label>
                                                    <input type="number" class="form-control form-control-sm" wire:model="ejerciciosWod.{{ $i }}.duracion" min="1" step="1" placeholder="Opcional" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
                                        {{-- Display mode --}}
                                        <div class="card mb-2 border shadow-sm" style="border-radius: 10px;">
                                            <div class="p-2 p-sm-3 bg-white">
                                                {{-- Mobile layout --}}
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

                                                {{-- Desktop layout --}}
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
                                                                    <span>s</span>
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
                        </div>
                        @endif
                    </div>

                    @if(!$editandoWod)
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" wire:click="cerrarModal" class="btn btn-secondary btn-sm">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-circle me-1"></i>Crear Clase
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
    @endif

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            padding: 1rem;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 1.25rem;
        }

        .btn-close-modal {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: #6c757d;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .btn-close-modal:hover {
            background: #f8f9fa;
            color: #000;
        }
    </style>

    {{-- Modal crear ejercicio --}}
    @if($mostrarModalCrearEjercicio)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1060; display: flex; align-items: center; justify-content: center;">
            <div class="bg-white rounded shadow-lg" style="width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0"><i class="bi bi-plus-square text-primary me-2"></i>Crear Nuevo Ejercicio</h5>
                    <button type="button" wire:click="cerrarModalCrearEjercicio" class="btn-close"></button>
                </div>
                <div class="p-3">
                    <form wire:submit.prevent="crearEjercicio">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Ejercicio <span class="text-danger">*</span></label>
                            <input type="text" wire:model="nuevo_ejercicio_nombre" class="form-control" placeholder="Ej: Sentadillas" required>
                            @error('nuevo_ejercicio_nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción (opcional)</label>
                            <textarea wire:model="nuevo_ejercicio_descripcion" class="form-control" rows="3" placeholder="Describe el ejercicio..."></textarea>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" wire:click="cerrarModalCrearEjercicio" class="btn btn-secondary btn-sm">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-circle me-1"></i>Crear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>