<div class="container py-2" 
     x-data="{ mostrarModal: false }"
     @abrir-modal.window="mostrarModal = true"
     @cerrar-modal.window="mostrarModal = false">

    {{-- Tabs de días --}}
    <div class="position-relative">
        <div class="day-selector d-flex overflow-auto gap-2 mb-3 px-1 pe-3" id="daySelector">
            @foreach ($dias as $dia)
                @php
                    $texto = $dia->isToday() ? 'Hoy' : $dia->format('D d');
                    $fecha = $dia->toDateString();
                @endphp

                <button 
                    wire:click="cambiarDia('{{ $fecha }}')"
                    class="day-tab {{ $diaSeleccionado === $fecha ? 'active' : '' }}">
                    {{ $texto }}
                </button>
            @endforeach
        </div>
        
        {{-- Gradiente de scroll --}}
        <div class="scroll-gradient" wire:ignore></div>
    </div>

    {{-- Lista de clases --}}
    <div class="d-flex flex-column gap-3">
        @forelse ($clasesDelDia as $clase)
            @include('livewire.athlete.partials.class-card')
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-calendar-x fs-1"></i>
                <p class="mt-2">No hay clases programadas para este día</p>
            </div>
        @endforelse
    </div>

    {{-- Modal de detalles --}}
    @include('livewire.athlete.partials.class-detail-modal')

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
    </style>
</div>