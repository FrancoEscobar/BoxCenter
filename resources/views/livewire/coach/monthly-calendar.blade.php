<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3" style="cursor: pointer;" wire:click="toggleCalendario">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-calendar3 text-primary me-2"></i>
                Vista Mensual
                <i class="bi bi-chevron-{{ $mostrarCalendario ? 'up' : 'down' }} ms-2" style="font-size: 1rem;"></i>
            </h5>
            @if($mostrarCalendario)
                <div class="d-flex gap-2" onclick="event.stopPropagation()">
                    <button wire:click="mesAnterior" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button wire:click="irAHoy" class="btn btn-outline-primary btn-sm">
                        Hoy
                    </button>
                    <button wire:click="mesSiguiente" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
    
    @if($mostrarCalendario)
    <div class="card-body p-0" x-data x-show="true" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
        <div class="calendar-grid">
            {{-- Encabezado de días de la semana --}}
            <div class="calendar-header">
                <div class="calendar-day-name">Lun</div>
                <div class="calendar-day-name">Mar</div>
                <div class="calendar-day-name">Mié</div>
                <div class="calendar-day-name">Jue</div>
                <div class="calendar-day-name">Vie</div>
                <div class="calendar-day-name">Sáb</div>
                <div class="calendar-day-name">Dom</div>
            </div>
            
            {{-- Días del mes --}}
            <div class="calendar-body">
                @foreach($diasDelMes as $dia)
                    @php
                        $fecha = $dia['fecha'];
                        $fechaStr = $fecha->format('Y-m-d');
                        $esHoy = $fecha->isToday();
                        $esMesActual = $dia['mesActual'];
                        $clases = $clasesPorDia[$fechaStr] ?? [];
                    @endphp
                    
                    <div class="calendar-day {{ !$esMesActual ? 'other-month' : '' }} {{ $esHoy ? 'today' : '' }}">
                        <div class="day-number">
                            {{ $fecha->day }}
                        </div>
                        
                        @if(count($clases) > 0)
                            <div class="day-classes">
                                @foreach(array_slice($clases, 0, 3) as $clase)
                                    <div 
                                        wire:click="abrirClase({{ $clase['id'] }})"
                                        class="class-item {{ $clase['estado'] === 'cancelada' ? 'cancelada' : ($clase['estado'] === 'realizada' ? 'realizada' : 'programada') }}"
                                        title="{{ $clase['tipo'] }} - {{ $clase['hora'] }}">
                                        <span class="class-time">{{ $clase['hora'] }}</span>
                                        <span class="class-type">{{ Str::limit($clase['tipo'], 10) }}</span>
                                    </div>
                                @endforeach
                                
                                @if(count($clases) > 3)
                                    <div class="more-classes">
                                        +{{ count($clases) - 3 }} más
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    <style>
        .calendar-grid {
            display: flex;
            flex-direction: column;
        }
    
    .calendar-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    .calendar-day-name {
        padding: 0.75rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.875rem;
        color: #495057;
    }
    
    .calendar-body {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: #dee2e6;
    }
    
    .calendar-day {
        min-height: 100px;
        background: white;
        padding: 0.5rem;
        position: relative;
        cursor: default;
    }
    
    .calendar-day.other-month {
        background: #f8f9fa;
        opacity: 0.6;
    }
    
    .calendar-day.today {
        background: #e7f3ff;
        border: 2px solid #4e73df;
    }
    
    .day-number {
        font-weight: 600;
        font-size: 0.875rem;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    
    .calendar-day.today .day-number {
        color: #4e73df;
    }
    
    .day-classes {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-top: 0.25rem;
    }
    
    .class-item {
        font-size: 0.7rem;
        padding: 2px 4px;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        gap: 4px;
        align-items: center;
        overflow: hidden;
    }
    
    .class-item.programada {
        background: #d1ecf1;
        color: #0c5460;
        border-left: 3px solid #17a2b8;
    }
    
    .class-item.realizada {
        background: #d4edda;
        color: #155724;
        border-left: 3px solid #28a745;
    }
    
    .class-item.cancelada {
        background: #f8d7da;
        color: #721c24;
        border-left: 3px solid #dc3545;
        text-decoration: line-through;
    }
    
    .class-item:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .class-time {
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .class-type {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .more-classes {
        font-size: 0.7rem;
        color: #6c757d;
        text-align: center;
        padding: 2px;
        font-style: italic;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .calendar-day {
            min-height: 80px;
            padding: 0.25rem;
        }
        
        .calendar-day-name {
            padding: 0.5rem;
            font-size: 0.75rem;
        }
        
        .day-number {
            font-size: 0.75rem;
        }
        
        .class-item {
            font-size: 0.65rem;
            padding: 1px 3px;
        }
        
        .class-type {
            display: none;
        }
    }
    </style>
</div>
