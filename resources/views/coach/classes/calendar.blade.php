@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">

    <div class="row align-items-stretch">

        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <h4 class="fw-bold">Calendario de Clases</h4>
                    <p class="text-muted">Visualiza y organiza tus clases según fecha, entrenamiento y disponibilidad.</p>

                    <button onclick="Livewire.dispatch('open-create-modal')" class="btn btn-success w-100 mb-3 shadow-sm fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Crear Nueva Clase
                    </button>

                    <hr>

                    <h5 class="fw-semibold mb-3">Filtros</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ir a fecha específica</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 bg-white" id="goto-date" placeholder="dd/mm/aaaa">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de entrenamiento</label>
                        <select class="form-select" id="filter-tipo">
                            <option value="">Todos</option>
                            @foreach ($tipos as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Coach</label>
                        <select class="form-select" id="filter-coach">
                            <option value="">Todos</option>
                            @foreach ($coaches as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select class="form-select" id="filter-estado">
                            <option value="">Todos</option>
                            @foreach ($estados as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cupo disponible</label>
                        <input type="number" min="0" class="form-control" id="filter-cupo" placeholder="Ej: 5">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Horario (desde-hasta)</label>
                        <div class="d-flex gap-1 align-items-center">
                            <input type="time" class="form-control form-control-sm" id="filter-hora-inicio" aria-label="Hora inicio">
                            <input type="time" class="form-control form-control-sm" id="filter-hora-fin" aria-label="Hora fin">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="btn-limpiar-horario" title="Limpiar horas">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9"> 
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-0 position-relative"> 
                    
                    <div id="calendar-loader" class="loading-overlay" style="display: none;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>

                    <div id="calendar" class="p-3"></div>

                </div>
            </div>
        </div> 
    </div> 
</div>

{{-- FullCalendar JS --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var calendarEl = document.getElementById("calendar");

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: "es",
        height: "auto",
        editable: false,
        displayEventEnd: true,
        
        // Configuración de la Barra Superior (Aquí está el botón de lista)
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth' // <--- ESTO ACTIVA LA VISTA DE LISTA
        },
        buttonText: { today: 'Hoy', month: 'Mes', list: 'Lista', week: 'Semana', day: 'Día' },

        // Loader visual
        loading: function(isLoading) {
            const loader = document.getElementById('calendar-loader');
            if (loader) loader.style.display = isLoading ? 'flex' : 'none';
        },

        // 1. CARGA DE EVENTOS
        events: function(fetchInfo, successCallback, failureCallback) {
            let filtros = {
                tipo: document.getElementById("filter-tipo")?.value,
                coach: document.getElementById("filter-coach")?.value,
                estado: document.getElementById("filter-estado")?.value,
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            };

            console.log("Solicitando eventos...", filtros); // DEBUG

            fetch('/coach/calendar/events?' + new URLSearchParams(filtros))
                .then(response => response.json())
                .then(events => {
                    console.log("Eventos recibidos:", events.length); // DEBUG
                    successCallback(events);
                })
                .catch(error => {
                    console.error("Error AJAX:", error);
                    failureCallback(error);
                });
        },

        // 2. DISEÑO VISUAL (Barra y Lista)
        eventContent: function(arg) {
            let props = arg.event.extendedProps;
            let color = arg.event.backgroundColor || '#6c757d';
            
            // Si es vista de LISTA, usamos diseño simple
            if (arg.view.type === 'listMonth') {
                return { html: `
                    <div class="d-flex justify-content-between w-100">
                        <span class="fw-bold" style="color: ${color}">${arg.event.title}</span>
                        <span class="small text-muted ms-2">Coach ${props.coach}</span>
                    </div>` 
                };
            } 
            
            // Si es vista de MES (Barra)
            let time = arg.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            let badgeInfo = `${props.inscriptos}/${props.cupo_total}`;
            
            return { html: `
                <div class="d-flex align-items-center px-1 overflow-hidden text-nowrap" 
                     style="border-left: 3px solid ${color}; font-size: 0.85em;">
                    <span class="fw-bold me-1">${time}</span>
                    <span class="fw-semibold text-truncate me-auto">${arg.event.title}</span>
                    <span class="bi-people badge bg-light text-dark border ms-1" style="font-size: 0.75em;"> ${badgeInfo}</span>
                </div>` 
            };
        },

        // --- POPOVER ---
        eventDidMount: function(info) {
            // No mostrar en lista
            if (info.view.type === 'listMonth' || info.view.type.includes('list')) return;

            try {
                if (typeof bootstrap === 'undefined') return;

                let props = info.event.extendedProps;
                // 1. CAPTURAMOS EL COLOR DEL EVENTO
                let color = info.event.backgroundColor || '#6c757d'; 
                let title = info.event.title;
                
                // Estado en mayúsculas
                let textoEstado = props.estado ? props.estado.toUpperCase() : '';

                // Formato de hora
                let formatOpts = {hour: '2-digit', minute:'2-digit', hour12: false};
                let horario = `${info.event.start.toLocaleTimeString([], formatOpts)} a ${info.event.end.toLocaleTimeString([], formatOpts)}`;
                let cupoTxt = `${props.inscriptos || 0}/${props.cupo_total || 0} inscritos`;

                // CONTENIDO (Cuerpo)
                let content = `
                    <div class="text-start small">
                        <div class="mb-1"><i class="bi bi-clock me-1 text-muted"></i> ${horario}</div>
                        <div class="mb-1"><i class="bi bi-person-badge me-1 text-muted"></i> Coach: <strong>${props.coach}</strong></div>
                        <div><i class="bi bi-people me-1 text-muted"></i> Cupo: <strong>${cupoTxt}</strong></div>
                    </div>`;

                // TÍTULO 
                let headerTitle = `
                    <div class="d-flex justify-content-between align-items-center text-white">
                        <span class="fw-bold text-truncate me-2" style="max-width: 160px;">${title}</span>
                        <span class="badge bg-white text-white bg-opacity-25" style="font-size: 0.65em;">${textoEstado}</span>
                    </div>`;

                // TEMPLATE
                let customTemplate = `
                    <div class="popover border-0 shadow" role="tooltip">
                        <div class="popover-arrow"></div>
                        <h3 class="popover-header text-white" style="background-color: ${color} !important;"></h3>
                        <div class="popover-body"></div>
                    </div>
                `;

                let popover = new bootstrap.Popover(info.el, {
                    title: headerTitle, // El título con HTML
                    content: content,
                    html: true,
                    trigger: 'hover',
                    placement: 'auto',
                    container: 'body',
                    template: customTemplate, // Usamos la plantilla con el color dinámico
                    sanitize: false // Permitir HTML en el contenido
                });
                
                info.el.popover = popover;

            } catch (e) {
                console.warn("Error al crear popover:", e);
            }
        },

        // Limpiar Popover
        eventWillUnmount: function(info) {
            if (info.el.popover) info.el.popover.dispose();
        },

        // Acciones
        eventClick: function(info) {
            Livewire.dispatch('open-view-modal', { claseId: info.event.id });
        },
        
        dateClick: function(info) {
            const today = new Date().toISOString().split('T')[0];
            if (info.dateStr >= today) {
                Livewire.dispatch('open-create-modal', { fecha: info.dateStr });
            }
        }
    });

    // Renderizar
    setTimeout(() => {
        calendar.render();
        console.log("Calendario renderizado");
    }, 100);

    // INTEGRACIÓN LIVEWIRE (Modales)
    const modalEl = document.getElementById('createClaseModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modalEl);
        Livewire.on('show-bootstrap-modal', () => bsModal.show());
        Livewire.on('hide-bootstrap-modal', () => bsModal.hide());
        Livewire.on('refresh-calendar', () => calendar.refetchEvents());
    }
    
    // FILTROS AUTOMÁTICOS
    document.querySelectorAll('#filter-tipo, #filter-coach, #filter-estado').forEach(el => {
        el.addEventListener('change', () => calendar.refetchEvents());
    });
});
</script>

{{-- Estilos --}}
<style>
    /* --- FUENTE Y GENERAL --- */
    #calendar {
        font-size: 14px !important;
    }

    /* --- BARRA DE HERRAMIENTAS Y BOTONES --- */
    .fc .fc-toolbar-title {
        font-weight: bold;
        font-size: 1.4rem;
        color: #000 !important;
        text-transform: capitalize !important; /* Ej: Noviembre 2025 */
    }

    .fc .fc-button-group {
        gap: 8px !important;
    }

    /* Botones Normales (Inactivos) */
    .fc .fc-button {
        border-radius: 8px !important;
        background-color: #6cb8ff !important;
        border-color: #6cb8ff !important;
        color: white !important;
        font-weight: 500;
        box-shadow: none !important;
    }

    .fc .fc-button:hover {
        background-color: #4da6ff !important;
        border-color: #4da6ff !important;
    }

    .fc .fc-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(108, 184, 255, 0.5) !important;
    }

    /* Botón Activo (Vista Seleccionada) */
    .fc .fc-button-active {
        background-color: #0d6efd !important; 
        border-color: #0d6efd !important;
        color: white !important;
        opacity: 1 !important;
        box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125) !important;
    }

    /* --- CABECERAS DEL CALENDARIO --- */
    .fc-col-header-cell-cushion {
        text-transform: uppercase !important;
        color: #555 !important;
        text-decoration: none !important;
        font-size: 0.8rem !important;
        font-weight: 700;
        padding: 8px 0 !important;
    }

    .fc-daygrid-day-number {
        color: #333 !important;
        text-decoration: none !important;
        font-weight: 600;
        padding: 4px 8px !important;
    }

    .fc-daygrid-day-number:hover {
        text-decoration: none !important;
        color: #000 !important;
    }

    #calendar a {
        text-decoration: none !important;
    }

    /* --- INTERACCIÓN CON LOS DÍAS (CLICS) --- */
    
    /* Días Futuros y Hoy: Se pueden clicar */
    .fc-daygrid-day-frame {
        cursor: pointer; /* Manito */
        padding: 2px !important;
        min-height: 100px !important;
        transition: background-color 0.2s;
    }
    
    .fc-daygrid-day-frame:hover {
        background-color: rgba(0,0,0,0.04); /* Gris suave al pasar mouse */
    }

    /* Días Pasados: NO se pueden clicar (Bloqueo visual) */
    .fc-day-past .fc-daygrid-day-frame {
        cursor: default !important; /* Flecha normal */
        background-color: #fdfdfd; /* Fondo sutilmente diferente */
    }

    /* Evitamos que el hover funcione en días pasados */
    .fc-day-past .fc-daygrid-day-frame:hover {
        background-color: #fdfdfd; 
    }

    /* --- DISEÑO DEL EVENTO (LA BARRA DE CLASE) --- */
    .fc-daygrid-event {
        border-radius: 4px !important;
        margin-top: 2px !important;
        border: none !important;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        
        /* Texto en NEGRO siempre */
        color: #000 !important; 
        text-decoration: none !important;
    }

    /* Contenido interno del evento (Flexbox para alinear hora - titulo - badge) */
    .fc-event-main {
        display: flex !important;
        align-items: center !important;
        width: 100%;
        padding: 2px 4px !important;
        white-space: nowrap !important; 
        overflow: hidden !important;
    }

    /* Efecto al pasar el mouse por el evento */
    .fc-daygrid-event:hover {
        filter: brightness(0.95);
        transform: scale(1.01);   
        z-index: 5; 
        transition: all 0.1s ease-in-out;
    }

    /* --- ESTILOS PARA LA VISTA DE LISTA --- */
    .fc-list-day-text,
    .fc-list-day-side-text,
    a.fc-list-day-text, 
    a.fc-list-day-side-text {
        color: #000 !important; /* Fecha en negro */
        text-decoration: none !important;
        font-weight: bold;
    }

    .fc-list-event {
        cursor: pointer !important; /* Manito */
        transition: background-color 0.1s;
    }

    .fc-list-event:hover td {
        background-color: rgba(0,0,0,0.05) !important; /* Gris suave */
    }

    /* --- OVERLAY DE CARGA (SPINNER) --- */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 50;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: inherit;
        backdrop-filter: blur(2px); 
    }

    /* --- AJUSTES DE CONTENEDORES --- */
    .card-calendar-container {
        max-width: 850px;
        margin: 0 auto;       
    }

    .card-calendar-container .card {
        padding: 1.5rem !important;
    }

    /* --- ESTILOS PARA EL POPOVER DE BOOTSTRAP --- */
    .popover-header {
        padding: 0.5rem 0.75rem !important;
        background-color: transparent !important;
        border-bottom: 0 !important;
    }

    .popover-body {
        padding: 0.75rem !important;
        color: #333;
    }

    .popover {
        border: 0 !important;
        overflow: hidden;
    }
</style>

@livewire('coach.create-clase-modal')
@livewire('coach.view-clase-modal')
@endsection