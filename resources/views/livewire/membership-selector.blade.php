<div class="container my-5">
    <h2 class="mb-4 text-center">Selecciona tu Membresía</h2>

    @if (session()->has('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Selección de actividad -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm p-3">
                <h4 class="text-center mb-3">Entrenamientos</h4>
                <div class="list-group">
                    @foreach ($tipos_entrenamiento as $entrenamiento)
                        <button wire:click="seleccionarEntrenamiento({{ $entrenamiento->id }})"
                            class="list-group-item list-group-item-action 
                            {{ $entrenamientoSeleccionado && $entrenamientoSeleccionado->id == $entrenamiento->id ? 'active' : '' }}">
                            {{ $entrenamiento->nombre }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Selección de plan -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm p-3">
                <h4 class="text-center mb-3">Planes</h4>
                <div class="list-group">
                    @foreach ($planes as $plan)
                        <button wire:click="seleccionarPlan({{ $plan->id }})"
                            class="list-group-item list-group-item-action 
                            {{ $planSeleccionado && $planSeleccionado->id == $plan->id ? 'active' : '' }}">
                            {{ $plan->nombre }} — ${{ number_format($plan->precio, 2) }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Botón para mostrar el resumen -->
    <div class="text-center mt-4">
        <button wire:click="verResumen" class="btn btn-primary btn-lg">
            Ver resumen y continuar
        </button>
    </div>

    <!-- Modal resumen -->
    @if ($mostrarResumen)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Resumen de Membresía</h5>
                        <button type="button" wire:click="$set('mostrarResumen', false)" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Entrenamiento:</strong> {{ $entrenamientoSeleccionado->nombre }}</p>
                        <p><strong>Plan:</strong> {{ $planSeleccionado->nombre }}</p>
                        <p><strong>Precio:</strong> ${{ number_format($planSeleccionado->precio, 2) }}</p>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="$set('mostrarResumen', false)" class="btn btn-secondary">Volver</button>
                        <button wire:click="continuarAlPago" class="btn btn-success">
                            Continuar al pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
