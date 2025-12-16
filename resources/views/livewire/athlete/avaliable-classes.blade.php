<div class="container py-2" 
     x-data="{ mostrarModal: false }"
     @abrir-modal.window="mostrarModal = true"
     @cerrar-modal.window="mostrarModal = false">

    {{-- Pestañas --}}
    <div class="mb-3">
        <div class="btn-group w-100" role="group">
            <button wire:click="cambiarVista('disponibles')" 
                    class="btn {{ $vistaActiva === 'disponibles' ? 'btn-primary' : 'btn-outline-primary' }}">
                Próximas
            </button>
            <button wire:click="cambiarVista('historial')" 
                    class="btn {{ $vistaActiva === 'historial' ? 'btn-primary' : 'btn-outline-primary' }}">
                Historial
            </button>
            <button wire:click="cambiarVista('membresia')" 
                    class="btn {{ $vistaActiva === 'membresia' ? 'btn-primary' : 'btn-outline-primary' }}">
                Mi Membresía
            </button>
        </div>
    </div>

    {{-- Contenido con fondo blanco --}}
    <div class="bg-white rounded shadow-sm p-3">
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
                                                    class="class-item programada"
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

            {{-- Lista de clases --}}
            @if(!$mostrarCalendarioMensual)
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
            @endif
        @elseif($vistaActiva === 'historial')
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
                    {{-- Controles de navegación mensual --}}
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
                                                    class="class-item programada"
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

            {{-- Lista de historial --}}
            @if(!$mostrarCalendarioHistorial)
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
        @elseif($vistaActiva === 'membresia')
            <div class="row">
                @if($membresia)
                    {{-- Información de Membresía --}}
                    <div class="col-12 col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">
                                    <i class="bi bi-card-checklist text-primary me-2"></i>Información de Membresía
                                </h5>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block">Plan</small>
                                    <strong class="fs-5">{{ $membresia->plan->nombre }}</strong>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Tipo de Entrenamiento</small>
                                    <span class="badge bg-primary">{{ $membresia->tipoEntrenamiento->nombre }}</span>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Fecha de Inicio</small>
                                    <strong>{{ \Carbon\Carbon::parse($membresia->fecha_inicio)->format('d/m/Y') }}</strong>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Fecha de Vencimiento</small>
                                    <strong class="{{ \Carbon\Carbon::parse($membresia->fecha_vencimiento)->isPast() ? 'text-danger' : 'text-success' }}">
                                        {{ \Carbon\Carbon::parse($membresia->fecha_vencimiento)->format('d/m/Y') }}
                                    </strong>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Estado</small>
                                    @php
                                        $hoy = \Carbon\Carbon::now();
                                        $fechaFin = \Carbon\Carbon::parse($membresia->fecha_vencimiento);
                                        $esActiva = $membresia->estado === 'activa' && $fechaFin->isFuture();
                                    @endphp
                                    @if($esActiva)
                                        <span class="badge bg-success">Activa</span>
                                    @elseif($membresia->estado === 'suspendida')
                                        <span class="badge bg-warning">Suspendida</span>
                                    @else
                                        <span class="badge bg-danger">Vencida</span>
                                    @endif
                                </div>

                                @if($esActiva && $fechaFin->diffInDays($hoy) <= 7)
                                    <div class="alert alert-warning small mb-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Tu membresía vence en {{ $fechaFin->diffInDays($hoy) }} días
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Historial de Pagos --}}
                    <div class="col-12 col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">
                                    <i class="bi bi-credit-card text-primary me-2"></i>Últimos Pagos
                                </h5>
                                
                                @if($pagosHistorial && $pagosHistorial->count() > 0)
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($pagosHistorial as $pago)
                                            <div class="border rounded p-2">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div>
                                                        <strong class="d-block">${{ number_format($pago->monto, 2) }}</strong>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($pago->created_at)->format('d/m/Y') }}</small>
                                                    </div>
                                                    @if($pago->estado === 'approved')
                                                        <span class="badge bg-success">Aprobado</span>
                                                    @elseif($pago->estado === 'pending')
                                                        <span class="badge bg-warning">Pendiente</span>
                                                    @else
                                                        <span class="badge bg-danger">Rechazado</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">
                                                    <i class="bi bi-credit-card-2-front me-1"></i>
                                                    {{ $pago->metodoPago->nombre ?? 'N/A' }}
                                                </small>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted fst-italic">No hay pagos registrados</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Plan Details --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3">
                                    <i class="bi bi-info-circle text-primary me-2"></i>Detalles del Plan
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <small class="text-muted d-block">Precio Mensual</small>
                                        <strong class="fs-5 text-primary">${{ number_format($membresia->plan->precio, 2) }}</strong>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <small class="text-muted d-block">Clases por Semana</small>
                                        <strong class="fs-5">{{ $membresia->plan->clases_por_semana }}</strong>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <small class="text-muted d-block">Acceso</small>
                                        <strong class="fs-5">{{ $membresia->plan->acceso_ilimitado ? 'Ilimitado' : 'Limitado' }}</strong>
                                    </div>
                                </div>

                                @if($membresia->plan->descripcion)
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Descripción</small>
                                        <p class="mb-0">{{ $membresia->plan->descripcion }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No tienes una membresía activa. Por favor contacta al administrador.
                        </div>
                    </div>
                @endif
            </div>
        @endif
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

        @media (max-width: 768px) {
            .clases-container {
                max-height: 400px;
            }
            
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
        }
    </style>
</div>