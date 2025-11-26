<div class="container py-2" 
     x-data="{ 
         diaSeleccionado: '{{ \Carbon\Carbon::today()->toDateString() }}',
         todasLasClases: @js($todasLasClases),
         get clasesDelDia() {
             return this.todasLasClases[this.diaSeleccionado] || [];
         }
     }">

    {{-- Tabs de días --}}
    <div class="position-relative">
        <div class="day-selector d-flex overflow-auto gap-2 mb-3 px-1 pe-3" id="daySelector">
            @foreach ($dias as $dia)
                @php
                    $texto = $dia->isToday() ? 'Hoy' : $dia->format('D d');
                    $fecha = $dia->toDateString();
                @endphp

                <button 
                    @click="diaSeleccionado = '{{ $fecha }}'"
                    :class="diaSeleccionado === '{{ $fecha }}' ? 'active' : ''"
                    class="day-tab">
                    {{ $texto }}
                </button>
            @endforeach
        </div>
        
        {{-- Gradiente de scroll --}}
        <div class="scroll-gradient" wire:ignore></div>
    </div>

    {{-- Lista de clases --}}
    <div class="d-flex flex-column gap-3">

        <template x-for="clase in clasesDelDia" :key="clase.id">
            <div>
                {{-- Card reservada --}}
                <div x-show="clase.reservada" 
                     class="card class-card border-0 shadow-sm rounded-4 p-3 bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <span x-text="clase.hora_inicio"></span> - <span x-text="clase.hora_fin"></span> — <span x-text="clase.tipo"></span>
                            </h5>
                            <p class="small mb-1">Coach: <span x-text="clase.coach"></span></p>
                            <span class="badge bg-light text-primary">Reservada</span>
                        </div>

                        <a class="text-white fw-semibold small" href="#">
                            Ver detalles →
                        </a>
                    </div>
                </div>

                {{-- Card sin cupo --}}
                <div x-show="!clase.reservada && clase.cupos === 0"
                     class="card class-card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <span x-text="clase.hora_inicio"></span> - <span x-text="clase.hora_fin"></span> — <span x-text="clase.tipo"></span>
                            </h5>
                            <p class="text-muted small mb-1">Coach: <span x-text="clase.coach"></span></p>
                            <span class="badge bg-danger">Sin cupo</span>
                        </div>

                        <span class="text-muted small fw-semibold">No disponible</span>
                    </div>
                </div>

                {{-- Card disponible --}}
                <div x-show="!clase.reservada && clase.cupos > 0"
                     class="card class-card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <span x-text="clase.hora_inicio"></span> - <span x-text="clase.hora_fin"></span> — <span x-text="clase.tipo"></span>
                            </h5>
                            <p class="text-muted small mb-1">Coach: <span x-text="clase.coach"></span></p>
                            <span class="badge bg-success"><span x-text="clase.cupos"></span> cupos disponibles</span>
                        </div>

                        <button 
                            @click="$wire.reservarClase(clase.id)"
                            class="btn btn-primary btn-sm rounded-pill fw-semibold">
                            Reservar un lugar
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>

    {{-- Estilos --}}
    <style>
        .day-selector {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .day-selector::-webkit-scrollbar { 
            display: none; 
        }

        .scroll-gradient {
            position: absolute;
            right: 0;
            top: 0;
            height: 40px;
            width: 80px;
            background: linear-gradient(to left, #f8f9fa 0%, #f8f9fa 30%, transparent 100%);
            pointer-events: none;
        }

        .day-tab {
            border: none;
            background: #f0f2f5;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
            min-width: 70px;
            flex-shrink: 0;
        }

        .day-tab.active {
            background: #4e73df;
            color: white;
        }

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
</div>