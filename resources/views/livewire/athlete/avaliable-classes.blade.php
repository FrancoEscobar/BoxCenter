<div class="container py-2" 
     x-data="{ mostrarModal: false }"
     @abrir-modal.window="mostrarModal = true"
     @cerrar-modal.window="mostrarModal = false">

    {{-- Pestañas estilo Chrome --}}
    <div class="d-flex justify-content-between align-items-end mb-0 flex-wrap gap-2">
        <div class="chrome-tabs-container">
            <button wire:click="cambiarVista('disponibles')" class="chrome-tab {{ $vistaActiva === 'disponibles' ? 'active' : '' }}">
                <span>Próximas</span>
            </button>
            <button wire:click="cambiarVista('historial')" class="chrome-tab {{ $vistaActiva === 'historial' ? 'active' : '' }}">
                <span>Historial</span>
            </button>
        </div>
    </div>

    {{-- Contenido con fondo blanco --}}
    <div class="content-container bg-white rounded-bottom shadow-sm p-3">
        @if($vistaActiva === 'disponibles')
            {{-- Tabs de días --}}
            <div class="position-relative">
                <div class="day-selector d-flex overflow-auto gap-2 mb-3 px-1 pe-3" id="daySelector">
                    @foreach ($dias as $dia)
                        @php
                            $texto = $dia->isToday() ? 'Hoy' : str_replace('.', '', ucfirst($dia->isoFormat('ddd D')));
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
            <div class="clases-container">
                @forelse ($clasesDelDia as $clase)
                    @include('livewire.athlete.partials.class-card')
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-1"></i>
                        <p class="mt-2">No hay clases programadas para este día</p>
                    </div>
                @endforelse
            </div>
        @else
            {{-- Tabs de días pasados --}}
            <div class="position-relative">
                <div class="day-selector d-flex overflow-auto gap-2 mb-3 px-1 pe-3" id="daySelectorHistorial">
                    @foreach ($dias as $dia)
                        @php
                            $texto = $dia->isToday() ? 'Hoy' : $dia->format('d/m');
                            $fecha = $dia->toDateString();
                        @endphp

                        <button 
                            wire:click="cambiarDia('{{ $fecha }}')"
                            class="day-tab-history {{ $diaSeleccionado === $fecha ? 'active' : '' }}">
                            {{ $texto }}
                        </button>
                    @endforeach
                </div>
                
                {{-- Gradiente de scroll --}}
                <div class="scroll-gradient" wire:ignore></div>
            </div>

            {{-- Lista de historial --}}
            <div class="clases-container">
                @forelse ($clasesHistorial as $clase)
                    @include('livewire.athlete.partials.class-card', ['clase' => $clase])
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-clock-history fs-1"></i>
                        <p class="mt-2">No hay clases en tu historial</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    {{-- Modal de detalles --}}
    @include('livewire.athlete.partials.class-detail-modal')

    {{-- Estilos --}}
    <style>
        /* Pestañas estilo Chrome */
        .chrome-tabs-container {
            display: flex;
            gap: 3px;
            align-items: flex-end;
            margin-bottom: -1px;
            position: relative;
            z-index: 2;
        }

        .chrome-tab {
            position: relative;
            background: transparent;
            border: none;
            padding: 0.375rem 0.75rem;
            border-radius: 6px 6px 0 0;
            font-weight: 500;
            font-size: 0.875rem;
            color: #5f6368;
            cursor: pointer;
            margin-bottom: 0;
            white-space: nowrap;
            box-shadow: none;
        }

        .chrome-tab:hover:not(.active) {
            background: rgba(255, 255, 255, 0.5);
        }

        .chrome-tab.active {
            background: white;
            color: #202124;
            font-weight: 600;
            box-shadow: none;
            z-index: 3;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid white;
        }

        .content-container {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0 6px 6px 6px;
            position: relative;
            z-index: 1;
        }

        .chrome-tab span {
            position: relative;
            z-index: 1;
        }

        .chrome-tab::before {
            content: '';
            position: absolute;
            left: -1px;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(0, 0, 0, 0.1);
        }

        .chrome-tab:first-child::before,
        .chrome-tab.active::before,
        .chrome-tab.active + .chrome-tab::before {
            display: none;
        }

        .day-selector {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .day-selector::-webkit-scrollbar { 
            display: none; 
        }

        .scroll-gradient {
            position: absolute;
            right: -12px;
            top: 0;
            height: 40px;
            width: 80px;
            background: linear-gradient(to left, white 0%, white 30%, transparent 100%);
            pointer-events: none;
        }

        .day-tab,
        .day-tab-history {
            border: none;
            background: #f0f2f5;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
            min-width: 70px;
            flex-shrink: 0;
        }

        .day-tab.active,
        .day-tab-history.active {
            background: #4e73df;
            color: white;
        }

        .clases-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .clases-container::-webkit-scrollbar {
            width: 8px;
        }

        .clases-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .clases-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .clases-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .clases-container {
                max-height: 400px;
            }
        }
    </style>
</div>