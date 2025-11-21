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

            // --- FILTROS ---
            if ($request->filled('tipo')) $query->where('tipo_entrenamiento_id', $request->tipo);
            if ($request->filled('coach')) $query->where('coach_id', $request->coach);
            if ($request->filled('estado')) $query->where('estado', $request->estado);
            if ($request->filled('cupo')) $query->where('cupo', '>=', $request->cupo);
            
            if ($request->filled('hora_inicio')) $query->whereTime('hora_inicio', '>=', $request->hora_inicio);
            if ($request->filled('hora_fin')) $query->whereTime('hora_fin', '<=', $request->hora_fin);

            $clases = $query->get();

            $events = $clases->map(function ($clase) {

                // Colores según estado de la clase
                $color = match ($clase->estado) {
                    'programada' => '#3788d8', // Azul
                    'realizada'  => '#28a745', // Verde
                    'cancelada'  => '#dc3545', // Rojo
                    default      => '#6c757d', // Gris
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
            });

            return response()->json($events);
        }
}