{{-- Card reservada --}}
@if($clase->reservada)
<div wire:click="abrirModal({{ $clase->id }})"
     class="card class-card border-0 shadow-sm rounded-4 p-3 bg-primary text-white"
     style="cursor: pointer;">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5 class="fw-bold mb-1">
                {{ $clase->hora_inicio }} - {{ $clase->hora_fin }} — {{ $clase->tipo }}
            </h5>
            <p class="small mb-1">Coach: {{ $clase->coach }}</p>
            <span class="badge bg-light text-primary">Reservada</span>
        </div>
    </div>
</div>

{{-- Card sin cupo --}}
@elseif($clase->cupos === 0)
<div wire:click="abrirModal({{ $clase->id }})"
     class="card class-card border-0 shadow-sm rounded-4 p-3"
     style="cursor: pointer;">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5 class="fw-bold mb-1">
                {{ $clase->hora_inicio }} - {{ $clase->hora_fin }} — {{ $clase->tipo }}
            </h5>
            <p class="text-muted small mb-1">Coach: {{ $clase->coach }}</p>
            <span class="badge bg-danger">Sin cupo</span>
        </div>

        <span class="text-muted small fw-semibold">No disponible</span>
    </div>
</div>

{{-- Card disponible --}}
@else
<div wire:click="abrirModal({{ $clase->id }})"
     class="card class-card border-0 shadow-sm rounded-4 p-3"
     style="cursor: pointer;">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5 class="fw-bold mb-1">
                {{ $clase->hora_inicio }} - {{ $clase->hora_fin }} — {{ $clase->tipo }}
            </h5>
            <p class="text-muted small mb-1">Coach: {{ $clase->coach }}</p>
            <span class="badge bg-success">{{ $clase->cupos }} cupos disponibles</span>
        </div>
    </div>
</div>
@endif

<style>
    .class-card {
        transition: transform 0.2s;
    }

    .class-card:active {
        transform: scale(0.97);
    }

    .bg-primary {
        background: linear-gradient(135deg, #4e73df, #224abe) !important;
    }
</style>
