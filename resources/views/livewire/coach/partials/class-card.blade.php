{{-- Card de clase --}}
<div wire:click="abrirDetalles({{ $clase->id }})"
     class="card class-card border-0 shadow-sm rounded-4 p-3 {{ $clase->estado === 'cancelada' ? 'opacity-75' : '' }}"
     style="cursor: pointer;">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5 class="fw-bold mb-1">
                {{ $clase->hora_inicio }} - {{ $clase->hora_fin }} — {{ $clase->tipo }}
            </h5>
            <p class="text-muted small mb-1"><i class="bi bi-person"></i> Coach: {{ $clase->coach_nombre }}</p>
            @if($clase->estado === 'cancelada')
                <span class="badge bg-danger">Clase Cancelada</span>
            @else
                <span class="badge" style="background: linear-gradient(135deg, #4e73df, #224abe);">{{ $clase->cupos }} cupos disponibles</span>
            @endif
        </div>
    </div>
</div>

<style>
    .class-card {
        transition: transform 0.2s;
    }

    .class-card:active {
        transform: scale(0.97);
    }
</style>

