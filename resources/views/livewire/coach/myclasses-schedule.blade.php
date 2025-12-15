<div class="container py-2" 
     x-data="{ mostrarModal: false }"
     @abrir-modal.window="mostrarModal = true"
     @cerrar-modal.window="mostrarModal = false">

    {{-- Pestañas estilo Chrome --}}
    <div class="chrome-tabs-container">
        <button wire:click="cambiarVista('mis-clases')" class="chrome-tab {{ $vistaActiva === 'mis-clases' ? 'active' : '' }}">
            <span>Próximas</span>
        </button>
        <button wire:click="cambiarVista('historial')" class="chrome-tab {{ $vistaActiva === 'historial' ? 'active' : '' }}">
            <span>Historial</span>
        </button>
        <button wire:click="cambiarVista('miembros')" class="chrome-tab {{ $vistaActiva === 'miembros' ? 'active' : '' }}">
            <span>Miembros</span>
        </button>
    </div>

    {{-- Contenido con fondo blanco --}}
    <div class="content-container bg-white rounded-bottom shadow-sm p-3">
        @if($vistaActiva === 'mis-clases')
            {{-- Vista de Mis Clases --}}
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

        {{-- Desplegable Vista Mensual --}}
        <div class="mb-3">
            <div class="border {{ $mostrarCalendarioMensual ? 'rounded-top' : 'rounded' }} p-2" style="cursor: pointer; background: #f8f9fa;" wire:click="toggleCalendarioMensual">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold small">
                        <i class="bi bi-calendar3 text-primary me-2"></i>Calendario
                    </span>
                    <i class="bi bi-chevron-{{ $mostrarCalendarioMensual ? 'up' : 'down' }}"></i>
                </div>
            </div>

            @if($mostrarCalendarioMensual)
                {{-- Controles de navegación mensual --}}
                <div class="d-flex justify-content-between align-items-center mt-2 mb-3" onclick="event.stopPropagation()">
                    <button wire:click="irAHoyMensual" class="btn btn-outline-primary btn-sm">
                        Hoy
                    </button>
                    <div class="d-flex gap-2">
                        <button wire:click="mesAnterior" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button wire:click="mesSiguiente" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                {{-- Calendario mensual --}}
                <div class="calendar-mensual-grid border border-top-0 rounded-bottom" style="background: #f8f9fa;">
                    {{-- Encabezado de días --}}
                    <div class="calendar-mensual-header">
                        <div class="calendar-mensual-day-name">Lun</div>
                        <div class="calendar-mensual-day-name">Mar</div>
                        <div class="calendar-mensual-day-name">Mié</div>
                        <div class="calendar-mensual-day-name">Jue</div>
                        <div class="calendar-mensual-day-name">Vie</div>
                        <div class="calendar-mensual-day-name">Sáb</div>
                        <div class="calendar-mensual-day-name">Dom</div>
                    </div>
                    
                    {{-- Días del mes --}}
                    <div class="calendar-mensual-body">
                        @foreach($diasDelMes as $dia)
                            @php
                                $fecha = $dia['fecha'];
                                $fechaStr = $fecha->format('Y-m-d');
                                $esHoy = $fecha->isToday();
                                $esMesActual = $dia['mesActual'];
                                $clases = $clasesPorDia[$fechaStr] ?? [];
                            @endphp
                            
                            <div class="calendar-mensual-day {{ !$esMesActual ? 'other-month' : '' }} {{ $esHoy ? 'today' : '' }}" 
                                 wire:click="irADiaDesdeCalendario('{{ $fechaStr }}')" style="cursor: pointer;">
                                <div class="day-number">{{ $fecha->day }}</div>
                                
                                @if(count($clases) > 0)
                                    <div class="day-classes">
                                        @foreach(array_slice($clases, 0, 2) as $clase)
                                            <div 
                                                class="class-item {{ $clase['estado'] === 'cancelada' ? 'cancelada' : ($clase['estado'] === 'realizada' ? 'realizada' : 'programada') }}"
                                                title="{{ $clase['tipo'] }} - {{ $clase['hora'] }}">
                                                <span class="class-time">{{ $clase['hora'] }}</span>
                                                <span class="class-type">{{ Str::limit($clase['tipo'], 10) }}</span>
                                            </div>
                                        @endforeach
                                        
                                        @if(count($clases) > 2)
                                            <div class="more-classes">+{{ count($clases) - 2 }} más</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Lista de clases (oculta cuando está el calendario) --}}
        @if(!$mostrarCalendarioMensual)
        <div class="clases-container">
            @forelse ($clasesDelDia as $clase)
                @include('livewire.coach.partials.class-card')
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mt-2">No hay clases programadas para este día</p>
                </div>
            @endforelse
        </div>
        @endif
        @elseif($vistaActiva === 'historial')
            {{-- Vista de Historial --}}
            {{-- Tabs de días (pasados) --}}
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

        {{-- Desplegable Vista Mensual Historial --}}
        <div class="mb-3">
            <div class="border {{ $mostrarCalendarioHistorial ? 'rounded-top' : 'rounded' }} p-2" style="cursor: pointer; background: #f8f9fa;" wire:click="toggleCalendarioHistorial">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold small">
                        <i class="bi bi-calendar3 text-primary me-2"></i>Calendario
                    </span>
                    <i class="bi bi-chevron-{{ $mostrarCalendarioHistorial ? 'up' : 'down' }}"></i>
                </div>
            </div>

            @if($mostrarCalendarioHistorial)
                {{-- Controles de navegación mensual historial --}}
                <div class="d-flex justify-content-between align-items-center mt-2 mb-3" onclick="event.stopPropagation()">
                    <button wire:click="irAHoyHistorial" class="btn btn-outline-primary btn-sm">
                        Hoy
                    </button>
                    <div class="d-flex gap-2">
                        <button wire:click="mesAnteriorHistorial" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button wire:click="mesSiguienteHistorial" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                {{-- Calendario mensual historial --}}
                <div class="calendar-mensual-grid border border-top-0 rounded-bottom" style="background: #f8f9fa;">
                    {{-- Encabezado de días --}}
                    <div class="calendar-mensual-header">
                        <div class="calendar-mensual-day-name">Lun</div>
                        <div class="calendar-mensual-day-name">Mar</div>
                        <div class="calendar-mensual-day-name">Mié</div>
                        <div class="calendar-mensual-day-name">Jue</div>
                        <div class="calendar-mensual-day-name">Vie</div>
                        <div class="calendar-mensual-day-name">Sáb</div>
                        <div class="calendar-mensual-day-name">Dom</div>
                    </div>
                    
                    {{-- Días del mes historial --}}
                    <div class="calendar-mensual-body">
                        @foreach($diasDelMesHistorial as $dia)
                            @php
                                $fecha = $dia['fecha'];
                                $fechaStr = $fecha->format('Y-m-d');
                                $esHoy = $fecha->isToday();
                                $esMesActual = $dia['mesActual'];
                                $clases = $clasesPorDiaHistorial[$fechaStr] ?? [];
                            @endphp
                            
                            <div class="calendar-mensual-day {{ !$esMesActual ? 'other-month' : '' }} {{ $esHoy ? 'today' : '' }}" 
                                 wire:click="irADiaDesdeCalendarioHistorial('{{ $fechaStr }}')" style="cursor: pointer;">
                                <div class="day-number">{{ $fecha->day }}</div>
                                
                                @if(count($clases) > 0)
                                    <div class="day-classes">
                                        @foreach(array_slice($clases, 0, 2) as $clase)
                                            <div 
                                                class="class-item {{ $clase['estado'] === 'cancelada' ? 'cancelada' : ($clase['estado'] === 'realizada' ? 'realizada' : 'programada') }}"
                                                title="{{ $clase['tipo'] }} - {{ $clase['hora'] }}">
                                                <span class="class-time">{{ $clase['hora'] }}</span>
                                                <span class="class-type">{{ Str::limit($clase['tipo'], 10) }}</span>
                                            </div>
                                        @endforeach
                                        
                                        @if(count($clases) > 2)
                                            <div class="more-classes">+{{ count($clases) - 2 }} más</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Lista de clases historial (oculta cuando está el calendario) --}}
        @if(!$mostrarCalendarioHistorial)
        <div class="clases-container">
            @forelse ($clasesHistorial as $clase)
                @include('livewire.coach.partials.class-card', ['clase' => $clase])
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-clock-history fs-1"></i>
                    <p class="mt-2">No hay clases para esta fecha</p>
                </div>
            @endforelse
        </div>
        @endif
        @else
            {{-- Vista de Miembros --}}
            {{-- Filtros de miembros --}}
            <div class="d-flex gap-2 mb-3 overflow-auto">
                <button 
                    wire:click="cambiarFiltroMiembros('todos')"
                    class="day-tab {{ $filtroMiembros === 'todos' ? 'active' : '' }}">
                    Todos
                </button>
                <button 
                    wire:click="cambiarFiltroMiembros('coaches')"
                    class="day-tab {{ $filtroMiembros === 'coaches' ? 'active' : '' }}">
                    Coaches
                </button>
                <button 
                    wire:click="cambiarFiltroMiembros('atletas')"
                    class="day-tab {{ $filtroMiembros === 'atletas' ? 'active' : '' }}">
                    Atletas
                </button>
            </div>

            {{-- Barra de búsqueda --}}
            <div class="mb-3">
                <div class="position-relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="busquedaMiembros"
                        class="form-control ps-5" 
                        placeholder="Buscar por nombre, apellido o email..."
                        style="border-radius: 20px; background: #f0f2f5; border: none;">
                    <i class="bi bi-search position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                </div>
            </div>

            <div class="miembros-container">
                @forelse ($miembros as $miembro)
                    <div class="miembro-card" wire:click="abrirUsuario({{ $miembro['id'] }})" style="cursor: pointer;">
                        <div class="d-flex align-items-center gap-3">
                            <img 
                                src="{{ $miembro['foto_perfil'] ?? asset('images/default-avatar.png') }}" 
                                alt="Foto de {{ $miembro['nombre'] }}" 
                                class="miembro-avatar"
                            >
                            <div class="flex-grow-1 miembro-info">
                                <h6 class="mb-1 fw-bold">{{ $miembro['nombre'] }} {{ $miembro['apellido'] }}</h6>
                                <p class="mb-0 text-muted small miembro-email">{{ $miembro['email'] }}</p>
                                @if($miembro['rol'] === 'atleta' && $miembro['estado_membresia'])
                                    <small class="badge bg-{{ $miembro['color_membresia'] }} mt-1">
                                        {{ $miembro['estado_membresia'] }}
                                    </small>
                                @endif
                            </div>
                            <span class="badge {{ $miembro['rol'] === 'coach' ? 'bg-primary' : 'bg-success' }}">
                                {{ ucfirst($miembro['rol']) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-people fs-1 text-muted mb-3"></i>
                        <p class="text-muted">No hay miembros registrados</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>{{-- Cierre de content-container --}}

    {{-- Botón flotante para crear clase --}}
    <button wire:click="abrirCrearClase" class="fab-button">
        <i class="bi bi-plus-circle me-2"></i>
        <span>Nueva Clase</span>
    </button>

    {{-- Modal de detalles (ViewClaseModal) --}}
    <livewire:coach.view-clase-modal />

    {{-- Modal de crear (CreateClaseModal) --}}
    <livewire:coach.create-clase-modal />

    {{-- Modal de usuario (ViewUserModal) --}}
    <livewire:coach.view-user-modal />

    {{-- Estilos --}}
    <style>
        /* Color anaranjado personalizado para badge sin membresía */
        .bg-orange {
            background-color: #ff8c00 !important;
            color: white !important;
        }
        
        /* Pestañas estilo Chrome */
        .chrome-tabs-container {
            display: flex;
            gap: 3px;
            align-items: flex-end;
            margin-bottom: -1px;
            position: relative;
            z-index: 2;
        }

        /* Botón flotante (FAB) extendido */
        .fab-button {
            position: fixed;
            bottom: 2rem;
            right: 1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 28px;
            background: #224abe;
            color: white;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            white-space: nowrap;
        }

        @media (min-width: 1200px) {
            .fab-button {
                right: calc((100vw - 1140px) / 2 + 1rem);
            }
        }

        .fab-button:hover {
            background: #1a3a8f;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
            transform: translateY(-2px);
        }

        .fab-button:active {
            transform: translateY(0);
        }

        .fab-button i {
            font-size: 1.25rem;
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

        /* Efecto de separación entre pestañas */
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

        .day-tab-history.active {
            background: #4e73df;
            color: white;
        }

        /* Calendario mensual */
        .calendar-mensual-grid {
            display: flex;
            flex-direction: column;
        }

        .calendar-mensual-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .calendar-mensual-day-name {
            padding: 0.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            color: #495057;
        }

        .calendar-mensual-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #dee2e6;
        }

        .calendar-mensual-day {
            min-height: 80px;
            background: white;
            padding: 0.25rem;
            position: relative;
        }

        .calendar-mensual-day.other-month {
            background: #f8f9fa;
            opacity: 0.6;
        }

        .calendar-mensual-day.today {
            background: #e7f3ff;
            border: 2px solid #4e73df;
        }

        .calendar-mensual-day .day-number {
            font-weight: 600;
            font-size: 0.75rem;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .calendar-mensual-day.today .day-number {
            color: #4e73df;
        }

        .calendar-mensual-day .day-classes {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 0.25rem;
        }

        .calendar-mensual-day .class-item {
            font-size: 0.65rem;
            padding: 2px 4px;
            border-radius: 3px;
            display: flex;
            gap: 4px;
            align-items: center;
            overflow: hidden;
        }

        .calendar-mensual-day .class-item.programada {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 3px solid #17a2b8;
        }

        .calendar-mensual-day .class-item.realizada {
            background: #d4edda;
            color: #155724;
            border-left: 3px solid #28a745;
        }

        .calendar-mensual-day .class-item.cancelada {
            background: #f8d7da;
            color: #721c24;
            border-left: 3px solid #dc3545;
            text-decoration: line-through;
        }



        .calendar-mensual-day .class-time {
            font-weight: 600;
            flex-shrink: 0;
        }

        .calendar-mensual-day .class-type {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .calendar-mensual-day .more-classes {
            font-size: 0.65rem;
            color: #6c757d;
            text-align: center;
            padding: 2px;
            font-style: italic;
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

        /* Contenedor de miembros */
        .miembros-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .miembros-container::-webkit-scrollbar {
            width: 8px;
        }

        .miembros-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .miembros-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .miembros-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .miembro-card {
            background: white;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .miembro-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
            border-color: #224abe;
        }

        .miembro-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e1e5eb;
        }

        .miembro-info {
            min-width: 0;
            overflow: hidden;
        }

        .miembro-email {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .calendar-mensual-day {
                min-height: 70px;
                padding: 0.2rem;
            }

            .calendar-mensual-day .class-type {
                display: none;
            }

            .clases-container,
            .miembros-container {
                max-height: 400px;
            }
        }
    </style>
</div>

