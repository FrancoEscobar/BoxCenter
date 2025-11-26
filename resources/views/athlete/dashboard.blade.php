@extends('layouts.app')

@section('content')
<div class="container">
    @if($proximaClase)
        {{-- Banner de próxima clase --}}
        <a href="" class="text-decoration-none text-white mb-3 w-100 d-block">
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
                            <strong>{{ \Carbon\Carbon::parse($proximaClase->clase->fecha)->translatedFormat('l j \d\e F') }}</strong> — 
                            {{ \Carbon\Carbon::parse($proximaClase->clase->hora_inicio)->format('H:i') }}
                        </p>
                        <small class="opacity-75">
                            {{ $proximaClase->clase->tipo_entrenamiento->nombre ?? 'Clase' }} 
                            @if($proximaClase->clase->coach)
                                • Coach: {{ $proximaClase->clase->coach->name }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </a>
    @else
        {{-- Banner si no hay clases reservadas --}}
        <div class="empty-state text-center mt-3 p-3 rounded-4 mb-3">
            <i class="bi bi-calendar-x fs-1 text-primary mb-2"></i>
            <h6 class="fw-bold mb-1">No tenés clases reservadas</h6>
            <p class="text-muted small mb-0">Cuando reserves una clase aparecerá acá.</p>
        </div>
    @endif
</div>

{{-- Estilos --}}
<style>
    .hover-shadow {
        transition: all 0.25s ease-in-out;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
    }

    .card:active {
        transform: scale(0.98);
        transition: 0.1s;
    }

    .bg-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .empty-state {
        background: #ffffff;
        border: 1px solid #e1e5eb;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
</style>

<livewire:athlete.avaliable-classes/>
@endsection

