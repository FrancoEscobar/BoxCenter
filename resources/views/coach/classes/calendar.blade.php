@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">

    <div class="row align-items-stretch">

        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <h4 class="fw-bold">Calendario de Clases</h4>
                    <p class="text-muted">Visualiza y organiza tus clases según fecha, entrenamiento y disponibilidad.</p>

                    <a href="" class="btn btn-success w-100 mb-3 shadow-sm fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Crear Nueva Clase
                    </a>

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
        // 1. Vista inicial por defecto
        initialView: 'dayGridMonth',
        locale: "es",
        height: "auto",
        editable: false,
        displayEventEnd: true,

        // 2. NUEVO: Configurar la barra superior para mostrar los botones de cambio
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth' // Aquí agregamos la vista de lista
        },

        // 3. NUEVO: Traducciones de los botones
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            list:  'Lista', // Nombre del botón para la vista de lista
            week:  'Semana',
            day:   'Día'
        },

        // Loader
        loading: function(isLoading) {
            const loader = document.getElementById('calendar-loader');
            if (loader) loader.style.display = isLoading ? 'flex' : 'none';
        },

        // Carga de datos
        events: function(fetchInfo, successCallback, failureCallback) {
            let filtros = {
                tipo: document.getElementById("filter-tipo")?.value,
                coach: document.getElementById("filter-coach")?.value,
                estado: document.getElementById("filter-estado")?.value,
                cupo: document.getElementById("filter-cupo")?.value,
                hora_inicio: document.getElementById("filter-hora-inicio")?.value,
                hora_fin: document.getElementById("filter-hora-fin")?.value,
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            };

            fetch('/coach/calendar/events?' + new URLSearchParams(filtros))
                .then(response => response.json())
                .then(events => successCallback(events))
                .catch(error => failureCallback(error));
        },

        // 4. DISEÑO (Adaptado para Lista y Grilla)
        eventContent: function(arg) {
            let props = arg.event.extendedProps;
            let title = arg.event.title;
            let inscriptos = props.inscriptos !== undefined ? props.inscriptos : 0;
            let total      = props.cupo_total !== undefined ? props.cupo_total : 0;
            
            // Clase para el badge (Rojo/Gris)
            let badgeClass = (inscriptos >= total) 
                ? 'bg-danger text-white border-danger' 
                : 'bg-light text-dark bg-opacity-75';

            // --- CASO A: VISTA DE LISTA (listMonth) ---
            if (arg.view.type === 'listMonth') {
                // En la lista, la hora ya sale en otra columna, así que no la ponemos aquí.
                // Mostramos: Título grande + Coach + Cupo a la derecha
                let coachName = props.coach ? props.coach : 'Sin Asignar';
                
                let html = `
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-uppercase">${title}</span>
                            <span class="small text-muted fst-italic">Coach ${coachName}</span>
                        </div>
                        <span class="badge ${badgeClass} ms-2" style="font-size: 0.85em;">
                            ${inscriptos}/${total}
                        </span>
                    </div>
                `;
                return { html: html };
            } 
            
            // --- CASO B: VISTA DE CALENDARIO (dayGridMonth) ---
            else {
                // Tu diseño original de barra compacta
                let formatTime = (date) => date ? date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false}) : '';
                let start = formatTime(arg.event.start);
                let end   = formatTime(arg.event.end);
                let timeStr = (start && end) ? `${start} a ${end}` : start;

                let html = `
                    <div class="d-flex align-items-center w-100 px-1 overflow-hidden text-nowrap">
                        <span class="fw-bold me-1" style="font-size: 0.8em;">${timeStr}</span>
                        <span class="me-1">-</span>
                        <span class="fw-semibold text-truncate me-auto" style="font-size: 0.9em;">${title}</span>
                        <span class="badge ${badgeClass} ms-1" style="font-size: 0.75em;">
                            ${inscriptos}/${total}
                        </span>
                    </div>
                `;
                return { html: html };
            }
        },

        eventClick: function(info) {
            window.location.href = `/coach/classes/${info.event.id}`;
        }
    });

    calendar.render();

    // --- Resto de lógica de filtros y botones (sin cambios) ---
    const filterIDs = ["filter-tipo", "filter-coach", "filter-estado", "filter-cupo", "filter-hora-inicio", "filter-hora-fin"];
    filterIDs.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.addEventListener("change", () => calendar.refetchEvents()); // Input para detectar cambios de fecha
    });

    const dateInput = document.getElementById("goto-date");
    if (dateInput) {
        flatpickr(dateInput, {
            locale: "es", dateFormat: "Y-m-d", altInput: true, altFormat: "d/m/Y", allowInput: true,
            onChange: function(selectedDates, dateStr) { if (dateStr) calendar.gotoDate(dateStr); }
        });
    }

    const btnLimpiarHorario = document.getElementById("btn-limpiar-horario");
    if (btnLimpiarHorario) {
        btnLimpiarHorario.addEventListener("click", () => {
            document.getElementById("filter-hora-inicio").value = "";
            document.getElementById("filter-hora-fin").value = "";
            calendar.refetchEvents();
        });
    }
});
</script>

{{-- Estilos --}}
<style>
    /* --- FUENTE GENERAL --- */
    #calendar {
        font-size: 14px !important;
    }

    /* --- BARRA DE HERRAMIENTAS Y BOTONES --- */
    .fc .fc-toolbar-title {
        font-weight: bold;
        font-size: 1.4rem;
        color: #000 !important;
        text-transform: capitalize !important; /* <--- AQUI EL CAMBIO: Capitaliza (Noviembre 2025) */
    }

    .fc .fc-button-group {
        gap: 8px !important;
    }

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

    .fc .fc-button-active {
        background-color: #0d6efd !important; 
        border-color: #0d6efd !important;
        color: white !important;
        opacity: 1 !important;
        box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125) !important;
    }

    /* --- CABECERAS DE DÍA (LUN, MAR...) --- */
    .fc-col-header-cell-cushion {
        text-transform: uppercase !important;
        color: #555 !important;
        text-decoration: none !important;
        font-size: 0.8rem !important;
        font-weight: 700;
        padding: 8px 0 !important;
    }

    /* --- NÚMEROS DE DÍA (1, 2, 3...) --- */
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

    /* --- DISEÑO DEL EVENTO (LA BARRA) --- */
    .fc-daygrid-event {
        border-radius: 4px !important;
        margin-top: 2px !important;
        border: none !important;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    /* CONTENIDO INTERNO DEL EVENTO */
    .fc-event-main {
        display: flex !important;
        align-items: center !important;
        width: 100%;
        padding: 2px 4px !important;
        white-space: nowrap !important; 
        overflow: hidden !important;
    }

    /* EFECTO HOVER */
    .fc-daygrid-event:hover {
        filter: brightness(0.95);
        transform: scale(1.01);   
        z-index: 5; 
        transition: all 0.1s ease-in-out;
    }

    /* --- AJUSTES DE CONTENEDORES --- */
    .card-calendar-container {
        max-width: 850px;
        margin: 0 auto;       
    }

    .card-calendar-container .card {
        padding: 1.5rem !important;
    }

    .fc .fc-daygrid-day-frame {
        padding: 2px !important;
        min-height: 100px !important;
    }

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

    .fc-daygrid-day-frame {
        cursor: pointer;
    }
    .fc-daygrid-day-frame:hover {
        background-color: rgba(0,0,0,0.02);
    }

    /* --- ESTILOS VISTA LISTA --- */
    .fc-list-day-text,
    .fc-list-day-side-text,
    a.fc-list-day-text, 
    a.fc-list-day-side-text {
        color: #000 !important;
        text-decoration: none !important;
    }
</style>
@endsection