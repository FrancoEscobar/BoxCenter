{{-- Modal de detalles de clase --}}
<div x-show="mostrarModal"
     x-transition.opacity
     @click.self="$wire.cerrarModal()"
     class="modal-overlay"
     style="display: none;">
    
    <div x-show="mostrarModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         @click.stop
         class="modal-content">
        
        @if($claseSeleccionada)
            <div>
                {{-- Header --}}
                <div class="modal-header">
                    <h5 class="fw-bold mb-0">{{ $claseSeleccionada->tipo }}</h5>
                    <button wire:click="cerrarModal" class="btn-close-modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    <div class="detail-item">
                        <i class="bi bi-clock text-primary"></i>
                        <div>
                            <small class="text-muted d-block">Horario</small>
                            <strong>{{ $claseSeleccionada->hora_inicio }} - {{ $claseSeleccionada->hora_fin }}</strong>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="bi bi-person text-primary"></i>
                        <div>
                            <small class="text-muted d-block">Coach</small>
                            <strong>{{ $claseSeleccionada->coach }}</strong>
                        </div>
                    </div>

                    @if(!($claseSeleccionada->es_historial ?? false))
                    <div class="detail-item">
                        <i class="bi bi-people text-primary"></i>
                        <div>
                            <small class="text-muted d-block">Cupos</small>
                            <strong>{{ $claseSeleccionada->cupos }} de {{ $claseSeleccionada->cupo_total }} disponibles</strong>
                        </div>
                    </div>
                    @endif

                    @if($claseSeleccionada->es_historial ?? false)
                    <div class="detail-item">
                        <i class="bi bi-journal-check text-primary"></i>
                        <div>
                            <small class="text-muted d-block">Estado</small>
                            @php
                                $estado = $claseSeleccionada->asistencia_estado ?? null;
                                $texto = match($estado) {
                                    'asistio' => 'Asististe',
                                    'ausente' => 'No asististe',
                                    default => 'Reservada',
                                };
                                $claseTexto = $estado === 'asistio' ? 'text-success' : ($estado === 'ausente' ? 'text-danger' : 'text-primary');
                            @endphp
                            <strong class="{{ $claseTexto }}">{{ $texto }}</strong>
                        </div>
                    </div>
                    @elseif($claseSeleccionada->reservada)
                    <div class="detail-item">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <div>
                            <small class="text-muted d-block">Estado</small>
                            <strong class="text-success">Clase reservada</strong>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                @unless($accionesDeshabilitadas ?? false)
                <div class="modal-footer">
                    @if($claseSeleccionada->reservada)
                        <button wire:click="cancelarReserva({{ $claseSeleccionada->id }})" 
                                class="btn btn-danger w-100 rounded-pill fw-semibold">
                            Cancelar reserva
                        </button>
                    @elseif($claseSeleccionada->cupos > 0)
                        <button wire:click="reservarClase({{ $claseSeleccionada->id }})" 
                                class="btn btn-primary w-100 rounded-pill fw-semibold">
                            Reservar un lugar
                        </button>
                    @else
                        <button disabled 
                                class="btn btn-secondary w-100 rounded-pill fw-semibold">
                            Sin cupos disponibles
                        </button>
                    @endif
                </div>
                @endunless
            </div>
        @endif
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal-content {
        background: white;
        border-radius: 1rem;
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid #e9ecef;
    }

    .btn-close-modal {
        border: none;
        background: #f0f2f5;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-close-modal:hover {
        background: #e4e6eb;
    }

    .modal-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .detail-item i {
        font-size: 1.25rem;
        margin-top: 0.25rem;
    }

    .modal-footer {
        padding: 1.25rem;
        border-top: 1px solid #e9ecef;
    }
</style>
