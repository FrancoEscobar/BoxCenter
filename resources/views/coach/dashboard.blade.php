@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mb-5">
        <h2 class="fw-bold">👋 Hola, {{ Auth::user()->name }}</h2>
        <p class="text-muted">Bienvenido a tu panel de entrenador</p>
    </div>

    {{-- Banner de próxima clase --}}
    @if(isset($nextClass))
        <div class="position-relative mb-5">
            <div class="card border-0 shadow-sm bg-primary text-white rounded-4 overflow-hidden">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" 
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-calendar-event fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Tu próxima clase</h5>
                            <p class="mb-0">
                                <strong>{{ \Carbon\Carbon::parse($nextClass->fecha)->translatedFormat('l j \d\e F') }}</strong> — 
                                {{ \Carbon\Carbon::parse($nextClass->hora_inicio)->format('H:i') }} a {{ \Carbon\Carbon::parse($nextClass->hora_fin)->format('H:i') }}<br>
                                <small class="text-white-50">Tipo: {{ $nextClass->tipo_entrenamiento->nombre ?? 'General' }}</small>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('coach.classes.show', $nextClass->id) }}" class="btn btn-light text-primary fw-semibold">
                        Ver clase
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-secondary text-center p-4 mb-5 rounded-4 shadow-sm">
            <h5 class="mb-0">No tienes clases programadas próximamente 🕓</h5>
        </div>
    @endif

    {{-- Accesos rápidos --}}
    <div class="row g-4">
        <div class="col-md-3">
            <a href="{{ route('coach.calendar') }}" class="text-decoration-none">
                <div class="card h-100 text-center p-4 hover-shadow">
                    <h4>📅 Clases</h4>
                    <p class="text-muted mb-0">Calendario y gestión de clases</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('coach.history') }}" class="text-decoration-none">
                <div class="card h-100 text-center p-4 hover-shadow">
                    <h4>📖 Historial</h4>
                    <p class="text-muted mb-0">Consulta tus clases pasadas</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('coach.wods') }}" class="text-decoration-none">
                <div class="card h-100 text-center p-4 hover-shadow">
                    <h4>🏋️‍♂️ WODs</h4>
                    <p class="text-muted mb-0">Crea o edita WODs</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="" class="text-decoration-none">
                <div class="card h-100 text-center p-4 hover-shadow">
                    <h4>⚙️ Ajustes</h4>
                    <p class="text-muted mb-0">Configura tu perfil y preferencias</p>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- Estilos --}}
<style>
    .hover-shadow {
        transition: all 0.25s ease-in-out;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .bg-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }
</style>
@endsection
