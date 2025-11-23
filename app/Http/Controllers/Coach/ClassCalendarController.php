<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Clase;
use App\Models\TipoEntrenamiento;
use App\Models\User;
use App\Models\Role;

class ClassCalendarController extends Controller
{
    public function index(): View
    {   
        // Datos para los filtros
        $tipos = TipoEntrenamiento::orderBy('nombre')->get();
        
        $coachRole = Role::where('nombre', 'coach')->first();
        $coaches = User::where('rol_id', $coachRole->id)->orderBy('name')->get();

        $estados = [
            'programada' => 'Programada',
            'realizada' => 'Realizada',
            'cancelada' => 'Cancelada',
        ];
        
        return view('coach.classes.calendar', compact('tipos', 'coaches', 'estados'));
    }

    // Endpoint para obtener los eventos del calendario con filtros
    public function events(Request $request)
        {   
            // Consulta base con relaciones y conteo de inscriptos
            $query = Clase::with(['tipo_entrenamiento', 'coach'])
                        ->withCount('cuposOcupados');

            // Rango de fechas obligatorio
        if ($request->has(['start', 'end'])) {
            // Cortamos la cadena ISO (2025-11-01T00:00...) a (2025-11-01)
            $start = substr($request->start, 0, 10);
            $end   = substr($request->end, 0, 10);
            
            $query->whereBetween('fecha', [$start, $end]);
        }

            // --- FILTROS ---
            if ($request->filled('tipo')) $query->where('tipo_entrenamiento_id', $request->tipo);
            if ($request->filled('coach')) $query->where('coach_id', $request->coach);
            if ($request->filled('estado')) $query->where('estado', $request->estado);
            if ($request->filled('cupo')) $query->where('cupo', '>=', $request->cupo);
            
            if ($request->filled('hora_inicio')) $query->whereTime('hora_inicio', '>=', $request->hora_inicio);
            if ($request->filled('hora_fin')) $query->whereTime('hora_fin', '<=', $request->hora_fin);

            $clases = $query->get();

            $events = $clases->map(function ($clase) {

                // Validar que la clase tenga fecha y hora definidas
                if (!$clase->fecha || !$clase->hora_inicio || !$clase->hora_fin) return null;

                // Colores según estado de la clase
                $color = match ($clase->estado) {
                    'programada' => '#3788d8', 
                    'realizada'  => '#28a745', 
                    'cancelada'  => '#dc3545', 
                    default      => '#6c757d', 
                };

                // Formateamos los datos para FullCalendar
                return [
                    'id'    => $clase->id,
                    'title' => $clase->tipo_entrenamiento->nombre ?? 'Clase',
                    'start' => $clase->fecha->format('Y-m-d') . 'T' . $clase->hora_inicio->format('H:i:s'),
                    'end'   => $clase->fecha->format('Y-m-d') . 'T' . $clase->hora_fin->format('H:i:s'),
                    
                    // Color de fondo del evento
                    'backgroundColor' => $color,
                    'borderColor'     => $color,

                    // DATOS EXTRA PARA EL DISEÑO
                    'extendedProps' => [
                        'coach'      => $clase->coach->name ?? 'Sin asignar', 
                        'cupo_total' => $clase->cupo,
                        'inscriptos' => $clase->cupos_ocupados_count,
                        'estado'     => $clase->estado
                    ]
                ];
            })->filter()->values(); // Eliminar nulos y reindexar

            return response()->json($events);
        }
}