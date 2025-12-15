<div>
    <div class="container">
        @if($proximaClase)
            {{-- Banner de próxima clase --}}
            <div wire:click="abrirDetalles" class="text-decoration-none text-white mb-3 w-100 d-block" style="cursor: pointer;">
                <div class="card border-0 shadow-sm bg-primary text-white rounded-4 w-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center" style="gap: 0.5rem;">
                                <i class="bi bi-bell fs-4"></i>
                                <h5 class="fw-bold mb-0">Tu próxima clase</h5>
                            </div>
                            <i class="bi bi-chevron-right fs-4"></i>
                        </div>
                        
                        <div class="mt-2">
                            <p class="mb-1">
                                <strong>{{ \Carbon\Carbon::parse($proximaClase->fecha)->translatedFormat('l j \d\e F') }}</strong> — 
                                {{ \Carbon\Carbon::parse($proximaClase->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($proximaClase->hora_fin)->format('H:i') }}
                            </p>
                            <small class="opacity-75">
                                {{ $proximaClase->tipo_entrenamiento->nombre ?? 'Clase' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Banner si no hay clases programadas --}}
            <div class="empty-state text-center mt-3 p-3 rounded-4 mb-3">
                <i class="bi bi-calendar-x fs-1 text-primary mb-2"></i>
                <h6 class="fw-bold mb-1">No tenés clases programadas</h6>
                <p class="text-muted small mb-0">Crea una nueva clase para comenzar.</p>
            </div>
        @endif
    </div>

    <style>
        .bg-primary {
            background: linear-gradient(135deg, #4e73df, #224abe);
        }

        .empty-state {
            background: #ffffff;
            border: 1px solid #e1e5eb;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
    </style>
</div>
