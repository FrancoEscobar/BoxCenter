<div class="container my-3 my-md-5 px-2 px-md-3">
    <h2 class="mb-3 mb-md-4 text-center fs-4 fs-md-2">Selecciona tu plan</h2>

    <p class="text-center text-muted mb-3 mb-md-5 small">
        Bienvenido/a a <strong>BoxCenter</strong>. Elegí el tipo de entrenamiento y el plan que mejor se adapten a tus objetivos.
        Recordá que podrás modificar tu membresía en cualquier momento desde tu panel de usuario.
    </p>

    @if (session()->has('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-2 g-md-3">
        <!-- Selección de actividad -->
        <div class="col-12 col-md-6 mb-3 mb-md-4">
            <div class="card shadow-sm p-2 p-md-3">
                <h4 class="text-center mb-2 mb-md-3 fs-5 fs-md-4">Entrenamientos</h4>
                <div class="list-group">
                    @foreach ($tipos_entrenamiento as $entrenamiento)
                        <button 
                            wire:click="seleccionarEntrenamiento({{ $entrenamiento->id }})"
                            class="list-group-item list-group-item-action py-2 py-md-3
                            {{ $entrenamientoSeleccionado && $entrenamientoSeleccionado->id == $entrenamiento->id ? 'active' : '' }}">
                            <div class="d-flex flex-column text-start">
                                <strong class="fs-6">{{ $entrenamiento->nombre }}</strong>
                                <small class="text-muted" style="font-size: 0.8rem;">{{ $entrenamiento->descripcion }}</small>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Selección de plan -->
        <div class="col-12 col-md-6 mb-3 mb-md-4">
            <div class="card shadow-sm p-2 p-md-3">
                <h4 class="text-center mb-2 mb-md-3 fs-5 fs-md-4">Planes</h4>
                <div class="list-group">
                    @foreach ($planes as $plan)
                        <button 
                            wire:click="seleccionarPlan({{ $plan->id }})"
                            class="list-group-item list-group-item-action py-2 py-md-3
                            {{ $planSeleccionado && $planSeleccionado->id == $plan->id ? 'active' : '' }}">
                            <div class="d-flex flex-column text-start">
                                <strong class="fs-6">{{ $plan->nombre }}</strong>
                                <small class="text-muted" style="font-size: 0.8rem;">{{ $plan->descripcion }}</small>
                                <span class="fw-bold mt-1 text-primary">${{ number_format($plan->precio, 2) }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Botón para mostrar el resumen -->
    <div class="text-center mt-3 mt-md-4 mb-3">
        <button wire:click="verResumen" class="btn btn-primary btn-lg w-100" style="max-width: 300px;">
            Continuar
        </button>
    </div>

    <!-- Modal resumen -->
    @if ($mostrarResumen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6);">
            <div class="modal-dialog modal-dialog-centered mx-2 mx-md-auto">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fs-6 fs-md-5">Resumen de Membresía</h5>
                        <button type="button" wire:click="$set('mostrarResumen', false)" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2"><strong>Entrenamiento:</strong> {{ $entrenamientoSeleccionado->nombre }}</p>
                        <p class="text-muted small mb-3">{{ $entrenamientoSeleccionado->descripcion }}</p>
                        <p class="mb-2"><strong>Plan:</strong> {{ $planSeleccionado->nombre }}</p>
                        <p class="text-muted small mb-3">{{ $planSeleccionado->descripcion }}</p>
                        <p class="mb-0"><strong>Precio:</strong> <span class="text-primary fs-5">${{ number_format($planSeleccionado->precio, 2) }}</span></p>
                    </div>
                    <div class="modal-footer flex-column flex-md-row gap-2">
                        <button wire:click="$set('mostrarResumen', false)" class="btn btn-secondary w-100 w-md-auto">Volver</button>
                        <button wire:click="continuarAlPago" class="btn btn-primary w-100 w-md-auto">
                            Continuar al pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
