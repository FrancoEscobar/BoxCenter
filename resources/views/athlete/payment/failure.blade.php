@extends('layouts.clean')

@section('title', 'Pago rechazado')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-5 text-center">
                <h2 class="mb-3 text-danger fw-bold">Tu pago no pudo completarse</h2>
                <p class="text-muted mb-4">
                    Mercado Pago no pudo procesar la transacción. Esto puede deberse a que la entidad emisora rechazó el pago, hubo un error en la validación de datos o el método de pago no está habilitado para esta operación.
                </p>

                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <a href="{{ route('athlete.planselection') }}" class="btn btn-danger btn-lg">
                        Intentar nuevamente
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#asistenciaModal">
                        Necesito ayuda
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de asistencia -->
<div class="modal fade" id="asistenciaModal" tabindex="-1" aria-labelledby="asistenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asistenciaModalLabel">Asistencia con el pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-start">
                <p class="mt-3">Si el inconveniente persiste, comunicate con nuestro equipo para que podamos asistirte:</p>
                <div class="p-3 bg-light rounded">
                    <p class="mb-1"><strong>Email:</strong> soporte@boxcenter.com</p>
                    <p class="mb-0"><strong>WhatsApp:</strong> +54 11 5555-5555</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection
