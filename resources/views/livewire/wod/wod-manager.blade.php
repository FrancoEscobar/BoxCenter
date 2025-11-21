<div>   
    <div>
        <div>
            <div class="alert alert-danger">
                <strong>Nota:</strong> Los ejercicios se guardan en el orden en que aparecen.
            </div>   
            <!-- Botones -->
            <div class="d-flex gap-2 mt-3">
                @if($modo !== 'ver')
                    <button type="button" class="btn btn-outline-primary" wire:click="agregarEjercicio">
                        + Agregar ejercicio
                    </button>

                    <button type="button" class="btn btn-outline-secondary" wire:click="openModalCrear">
                        + Crear ejercicio
                    </button>
                @endif
            </div>

            <!-- Tarjetas dinámicas -->
            <div class="mt-4">

                @foreach ($ejercicios as $i => $e)
                    <div class="card p-3 mb-3 {{ $modo === 'ver' ? 'bg-light border-0' : '' }}">
                        <div class="row">

                            <input type="hidden" name="ejercicios[{{ $i }}][orden]" value="{{ $i + 1 }}">

                            <!-- 1. SELECT EJERCICIO -->
                            <div class="col-md-4">
                                <label>Ejercicio</label>
                                <select class="form-select" 
                                    wire:model="ejercicios.{{ $i }}.id" 
                                    name="ejercicios[{{ $i }}][id]" 
                                    required 
                                    oninvalid="this.setCustomValidity('Debes ingresar al menos un ejercicio')" 
                                    oninput="this.setCustomValidity('')"
                                    {{ $modo === 'ver' ? 'disabled' : '' }}
                                >
                                    <option value="" disabled>Seleccionar…</option>
                                    @foreach ($listaEjercicios as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 2. INPUT SERIES -->
                            <div class="col-md-2">
                                <label>Series</label>
                                <input type="number" class="form-control"
                                    wire:model="ejercicios.{{ $i }}.series" 
                                    min="1" 
                                    name="ejercicios[{{ $i }}][series]" 
                                    required 
                                    onkeydown="if(event.key === '-' || event.key === '+') event.preventDefault();" 
                                    oninvalid="this.setCustomValidity('Este campo es obligatorio')" 
                                    oninput="this.setCustomValidity('')"
                                    {{ $modo === 'ver' ? 'disabled' : '' }}
                                >
                            </div>

                            <!-- 3. INPUT REPETICIONES -->
                            <div class="col-md-2">
                                <label>Reps</label>
                                <input type="number" class="form-control"
                                    wire:model="ejercicios.{{ $i }}.repeticiones" 
                                    min="1" 
                                    name="ejercicios[{{ $i }}][repeticiones]" 
                                    required 
                                    onkeydown="if(event.key === '-' || event.key === '+') event.preventDefault();"
                                    oninvalid="this.setCustomValidity('Este campo es obligatorio')" 
                                    oninput="this.setCustomValidity('')"
                                    {{ $modo === 'ver' ? 'disabled' : '' }}
                                >
                            </div>

                            <!-- 4. INPUT DURACIÓN OPCIONAL -->
                            <div class="col-md-2">
                                <label>Duración (seg. Opcional)</label>
                                <input type="number" class="form-control"
                                    wire:model="ejercicios.{{ $i }}.duracion" 
                                    min="1" 
                                    name="ejercicios[{{ $i }}][duracion]" 
                                    onkeydown="if(event.key === '-' || event.key === '+') event.preventDefault();"
                                    {{ $modo === 'ver' ? 'disabled' : '' }}
                                >
                            </div>

                            @if($modo !== 'ver')
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button"
                                        class="btn btn-danger"
                                        wire:click="eliminarEjercicio({{ $i }})">X</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>

        </div>

        <div>
            <div wire:ignore.self class="modal fade" id="modalCrearEjercicio" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Crear ejercicio</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre del ejercicio</label>
                                <input type="text" class="form-control" wire:model="nuevoNombre">
                                @error('nuevoNombre') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" wire:model="nuevaDescripcion"></textarea>
                                @error('nuevaDescripcion') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            
                            <button type="button" class="btn btn-primary" wire:click="guardarEjercicio">Guardar</button>
                        </div>

                    </div>
                </div>    
            </div>
        </div>
    </div>
</div> 