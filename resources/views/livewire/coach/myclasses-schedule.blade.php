<div class="container py-2" 
     x-data="{ mostrarModal: false }"
     @abrir-modal.window="mostrarModal = true"
     @cerrar-modal.window="mostrarModal = false">

    {{-- Pestañas --}}
    <div class="mb-3">
        <div class="btn-group w-100" role="group">
            <button wire:click="cambiarVista('mis-clases')" 
                    class="btn {{ $vistaActiva === 'mis-clases' ? 'btn-primary' : 'btn-outline-primary' }}">
                Próximas
            </button>
            <button wire:click="cambiarVista('historial')" 
                    class="btn {{ $vistaActiva === 'historial' ? 'btn-primary' : 'btn-outline-primary' }}">
                Historial
            </button>
            <button wire:click="cambiarVista('miembros')" 
                    class="btn {{ $vistaActiva === 'miembros' ? 'btn-primary' : 'btn-outline-primary' }}">
                Miembros
            </button>
            <button wire:click="cambiarVista('pagos')" 
                    class="btn {{ $vistaActiva === 'pagos' ? 'btn-primary' : 'btn-outline-primary' }}">
                Pagos
            </button>
        </div>
    </div>

    {{-- Contenido con fondo blanco --}}
    <div class="bg-white rounded shadow-sm p-3">
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
        @elseif($vistaActiva === 'miembros')
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
        @elseif($vistaActiva === 'pagos')
            {{-- Vista de Historial de Pagos --}}
            <div class="mb-3">
                <h5 class="mb-3 fw-bold">
                    <i class="bi bi-cash-coin me-2"></i>Historial de Pagos
                </h5>
            </div>

            {{-- Filtros de pagos --}}
            <div class="row g-2 mb-3">
                <div class="col-3">
                    <button 
                        wire:click="cambiarFiltroPagos('todos')"
                        class="day-tab w-100 {{ $filtroPagos === 'todos' ? 'active' : '' }}"
                        style="font-size: 0.85rem; padding: 8px 4px;">
                        Todos
                    </button>
                </div>
                <div class="col-3">
                    <button 
                        wire:click="cambiarFiltroPagos('aprobados')"
                        class="day-tab w-100 {{ $filtroPagos === 'aprobados' ? 'active' : '' }}"
                        style="font-size: 0.85rem; padding: 8px 4px;">
                        Aprobados
                    </button>
                </div>
                <div class="col-3">
                    <button 
                        wire:click="cambiarFiltroPagos('pendientes')"
                        class="day-tab w-100 {{ $filtroPagos === 'pendientes' ? 'active' : '' }}"
                        style="font-size: 0.85rem; padding: 8px 4px;">
                        Pendientes
                    </button>
                </div>
                <div class="col-3">
                    <button 
                        wire:click="cambiarFiltroPagos('rechazados')"
                        class="day-tab w-100 {{ $filtroPagos === 'rechazados' ? 'active' : '' }}"
                        style="font-size: 0.85rem; padding: 8px 4px;">
                        Rechazados
                    </button>
                </div>
            </div>

            {{-- Barra de búsqueda --}}
            <div class="mb-3">
                <div class="position-relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="busquedaPagos"
                        class="form-control ps-5" 
                        placeholder="Buscar por nombre o email..."
                        style="border-radius: 20px; background: #f0f2f5; border: none;">
                    <i class="bi bi-search position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                </div>
            </div>

            {{-- Tarjetas de pagos --}}
            <div class="pagos-container">
                @forelse ($pagos as $pago)
                    <div class="pago-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">{{ $pago['usuario_nombre'] }} {{ $pago['usuario_apellido'] }}</h6>
                                <p class="mb-0 text-muted small">{{ $pago['usuario_email'] }}</p>
                            </div>
                            <div class="text-end">
                                @if($pago['status'] === 'approved')
                                    <span class="badge bg-success mb-1">Aprobado</span>
                                @elseif($pago['status'] === 'pending')
                                    <span class="badge bg-warning mb-1">Pendiente</span>
                                @elseif($pago['status'] === 'rejected')
                                    <span class="badge bg-danger mb-1">Rechazado</span>
                                @else
                                    <span class="badge bg-secondary mb-1">{{ $pago['status'] }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bi bi-calendar3 text-muted small"></i>
                                    <small class="text-muted">{{ Carbon\Carbon::parse($pago['fecha'])->format('d/m/Y') }}</small>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <span class="fw-bold text-success fs-5">${{ number_format($pago['importe'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-2">
                            <span class="badge bg-primary">{{ $pago['plan'] }}</span>
                            <span class="badge bg-info">{{ $pago['tipo_entrenamiento'] }}</span>
                        </div>

                        @if($pago['fecha_aprobacion'] !== 'N/A')
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-check-circle text-success small"></i>
                                <small class="text-muted">Aprobado: {{ $pago['fecha_aprobacion'] }}</small>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-receipt fs-1 text-muted mb-3"></i>
                        <p class="text-muted">No hay pagos registrados</p>
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

        /* Contenedor de pagos */
        .pagos-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .pagos-container::-webkit-scrollbar {
            width: 8px;
        }

        .pagos-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .pagos-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .pagos-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Tarjeta de pago */
        .pago-card {
            background: white;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .pago-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
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
                min-height: 50px;
                padding: 0.15rem;
            }

            .calendar-mensual-day .day-number {
                font-size: 0.65rem;
                margin-bottom: 0.1rem;
            }

            .calendar-mensual-day .class-item {
                font-size: 0.55rem;
                padding: 1px 2px;
                gap: 2px;
            }

            .calendar-mensual-day .class-time {
                display: none;
            }

            .calendar-mensual-day .class-type {
                font-size: 0.6rem;
            }

            .calendar-mensual-day .more-classes {
                font-size: 0.55rem;
                padding: 1px;
            }

            .calendar-mensual-day-name {
                padding: 0.3rem;
                font-size: 0.65rem;
            }

            .clases-container,
            .miembros-container,
            .pagos-container {
                max-height: 400px;
            }
        }
    </style>
</div>

