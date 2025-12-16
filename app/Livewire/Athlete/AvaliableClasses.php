<?php

namespace App\Livewire\Athlete;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Clase;
use App\Models\Asistencia;
use Illuminate\Support\Facades\Auth;

class AvaliableClasses extends Component
{
    public $dias = [];
    public $diaSeleccionado;
    public $clasesDelDia = [];
    public $clasesHistorial = [];
    public $claseSeleccionada = null;
    public $vistaActiva = 'disponibles';
    public $accionesDeshabilitadas = false;
    public $mostrarCalendarioMensual = false;
    public $mesActual;
    public $anioActual;
    public $diasDelMes = [];
    public $clasesPorDia = [];
    
    // Propiedades para calendario de historial
    public $mostrarCalendarioHistorial = false;
    public $mesHistorial;
    public $anioHistorial;
    public $diasDelMesHistorial = [];
    public $clasesPorDiaHistorial = [];
    
    // Propiedades para membresía
    public $membresia = null;
    public $pagosHistorial = [];

    public function mount()
    {
        // Próximos 60 días incluyendo hoy
        $this->dias = collect(range(0, 59))->map(function ($i) {
            return Carbon::today()->addDays($i);
        });

        $this->diaSeleccionado = Carbon::today()->toDateString();
        $this->mesActual = Carbon::now()->month;
        $this->anioActual = Carbon::now()->year;
        $this->mesHistorial = Carbon::now()->month;
        $this->anioHistorial = Carbon::now()->year;
        $this->cargarClases();
        $this->cargarHistorial();
        $this->cargarMembresia();
    }

    public function cambiarVista($vista)
    {
        $this->vistaActiva = $vista;

        if ($vista === 'disponibles') {
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->addDays($i);
            });
        } elseif ($vista === 'membresia') {
            $this->cargarMembresia();
        } else {
            // Últimos 60 días incluyendo hoy (ordenados de más antiguo a hoy)
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->subDays(59 - $i);
            });
        }

        $this->diaSeleccionado = Carbon::today()->toDateString();
        $this->cargarClases();
        $this->cargarHistorial();
    }

    public function cambiarDia($fecha)
    {
        $this->diaSeleccionado = $fecha;
        // Recalcular rango de días según la vista activa para mantener el orden esperado
        if ($this->vistaActiva === 'disponibles') {
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->addDays($i);
            });
        } else {
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->subDays(59 - $i);
            });
        }

        $this->cargarClases();
        $this->cargarHistorial();
    }

    public function abrirModal($claseId, $desdeHistorial = false)
    {
        $coleccion = $desdeHistorial ? $this->clasesHistorial : $this->clasesDelDia;
        $clase = collect($coleccion)->firstWhere('id', $claseId);
        $this->claseSeleccionada = $clase;
        $this->accionesDeshabilitadas = (bool) $desdeHistorial;
        $this->dispatch('abrir-modal');
    }

    public function cerrarModal()
    {
        $this->claseSeleccionada = null;
        $this->accionesDeshabilitadas = false;
        $this->dispatch('cerrar-modal');
    }

    public function cargarClases()
    {
        if ($this->vistaActiva !== 'disponibles') {
            return;
        }

        $clases = Clase::whereDate('fecha', $this->diaSeleccionado)
            ->where('estado', 'programada')
            ->with(['tipo_entrenamiento', 'coach', 'wod.ejercicios'])
            ->withCount(['cuposOcupados' => function ($query) {
                $query->where('estado', '!=', 'cancelo');
            }])
            ->orderBy('hora_inicio')
            ->get();

        // Obtener las clases reservadas del usuario
        $reservasUsuario = Asistencia::where('usuario_id', Auth::id())
            ->whereIn('estado', ['reservo', 'ausente'])
            ->pluck('clase_id')
            ->toArray();

        $this->clasesDelDia = $clases->map(function ($clase) use ($reservasUsuario) {
            $cuposDisponibles = $clase->cupo - $clase->cupos_ocupados_count;
            
            return (object)[
                'id' => $clase->id,
                'hora_inicio' => $clase->hora_inicio->format('H:i'),
                'hora_fin' => $clase->hora_fin->format('H:i'),
                'tipo' => $clase->tipo_entrenamiento->nombre ?? 'Clase',
                'coach' => $clase->coach->name ?? 'Sin asignar',
                'cupos' => $cuposDisponibles,
                'cupo_total' => $clase->cupo,
                'reservada' => in_array($clase->id, $reservasUsuario),
                'es_historial' => false,
                'wod' => $clase->wod,
            ];
        })->toArray();
    }

    public function cargarHistorial()
    {
        if ($this->vistaActiva !== 'historial') {
            $this->clasesHistorial = [];
            return;
        }

        $clases = Clase::whereDate('fecha', $this->diaSeleccionado)
            ->where('fecha', '<', Carbon::today())
            ->whereHas('asistencias', function ($query) {
                $query->where('usuario_id', Auth::id())
                    ->whereIn('estado', ['asistio', 'ausente', 'reservo']);
            })
            ->with(['tipo_entrenamiento', 'coach', 'wod.ejercicios', 'asistencias' => function ($query) {
                $query->where('usuario_id', Auth::id());
            }])
            ->withCount(['cuposOcupados' => function ($query) {
                $query->where('estado', '!=', 'cancelo');
            }])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();

        $this->clasesHistorial = $clases->map(function ($clase) {
            $cuposDisponibles = $clase->cupo - $clase->cupos_ocupados_count;
            $asistencia = $clase->asistencias->first();

            return (object) [
                'id' => $clase->id,
                'fecha' => $clase->fecha->format('d/m/Y'),
                'hora_inicio' => $clase->hora_inicio->format('H:i'),
                'hora_fin' => $clase->hora_fin->format('H:i'),
                'tipo' => $clase->tipo_entrenamiento->nombre ?? 'Clase',
                'coach' => $clase->coach->name ?? 'Sin asignar',
                'cupos' => $cuposDisponibles,
                'cupo_total' => $clase->cupo,
                'reservada' => $asistencia?->estado === 'reservo',
                'asistencia_estado' => $asistencia?->estado,
                'es_historial' => true,
                'wod' => $clase->wod,
            ];
        })->toArray();
    }

    public function cargarMembresia()
    {
        $usuario = Auth::user();
        
        $this->membresia = \App\Models\Membresia::where('usuario_id', $usuario->id)
            ->with(['plan', 'tipoEntrenamiento'])
            ->first();

        if ($this->membresia) {
            $this->pagosHistorial = \App\Models\Pago::where('membresia_id', $this->membresia->id)
                ->with(['metodoPago'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
    }

    public function reservarClase($claseId)
    {
        try {
            // Verificar si la clase existe y está disponible
            $clase = Clase::findOrFail($claseId);

            // Verificar que la clase esté programada
            if ($clase->estado !== 'programada') {
                session()->flash('error', 'Esta clase ya no está disponible.');
                $this->dispatch('reserva-error');
                return;
            }

            // Verificar si ya tiene una reserva
            $reservaExistente = Asistencia::where('usuario_id', Auth::id())
                ->where('clase_id', $claseId)
                ->whereIn('estado', ['reservo', 'ausente', 'asistio'])
                ->first();

            if ($reservaExistente) {
                session()->flash('error', 'Ya tenés esta clase reservada.');
                $this->dispatch('reserva-error');
                return;
            }

            // Verificar cupos disponibles
            $cuposOcupados = Asistencia::where('clase_id', $claseId)
                ->where('estado', '!=', 'cancelo')
                ->count();

            if ($cuposOcupados >= $clase->cupo) {
                session()->flash('error', 'No hay cupos disponibles para esta clase.');
                $this->dispatch('reserva-error');
                return;
            }

            // Crear la reserva
            Asistencia::create([
                'clase_id' => $claseId,
                'usuario_id' => Auth::id(),
                'estado' => 'reservo',
            ]);

            session()->flash('success', 'Clase reservada correctamente.');
            $this->cargarClases();
            $this->cerrarModal();
            $this->dispatch('reserva-actualizada');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al reservar la clase.');
            $this->dispatch('reserva-error');
        }
    }

    public function cancelarReserva($claseId)
    {
        try {
            // Buscar la reserva del usuario para esta clase
            $reserva = Asistencia::where('usuario_id', Auth::id())
                ->where('clase_id', $claseId)
                ->whereIn('estado', ['reservo', 'ausente'])
                ->first();

            if (!$reserva) {
                session()->flash('error', 'No tenés una reserva para esta clase.');
                $this->dispatch('reserva-error');
                return;
            }

            // Cambiar el estado a 'cancelo'
            $reserva->update(['estado' => 'cancelo']);

            session()->flash('success', 'Reserva cancelada correctamente.');
            $this->cargarClases();
            $this->cerrarModal();
            $this->dispatch('reserva-actualizada');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al cancelar la reserva: ' . $e->getMessage());
            $this->dispatch('reserva-error');
        }
    }

    // Métodos del calendario mensual (vista próximas)
    public function toggleCalendarioMensual()
    {
        $this->mostrarCalendarioMensual = !$this->mostrarCalendarioMensual;
        if ($this->mostrarCalendarioMensual) {
            $this->generarDiasDelMes();
        }
    }

    public function mesAnterior()
    {
        $fecha = Carbon::create($this->anioActual, $this->mesActual, 1)->subMonth();
        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;
        $this->generarDiasDelMes();
    }

    public function mesSiguiente()
    {
        $fecha = Carbon::create($this->anioActual, $this->mesActual, 1)->addMonth();
        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;
        $this->generarDiasDelMes();
    }

    public function irAHoyMensual()
    {
        $this->mesActual = Carbon::now()->month;
        $this->anioActual = Carbon::now()->year;
        $this->diaSeleccionado = Carbon::today()->toDateString();
        $this->generarDiasDelMes();
        $this->cargarClases();
    }

    public function irADiaDesdeCalendario($fecha)
    {
        $fechaSeleccionada = Carbon::parse($fecha);
        
        // Si la fecha es pasada, cambiar a vista historial
        if ($fechaSeleccionada->lt(Carbon::today())) {
            $this->vistaActiva = 'historial';
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->subDays(59 - $i);
            });
        }
        
        $this->diaSeleccionado = $fecha;
        $this->cargarClases();
        $this->cargarHistorial();
        $this->mostrarCalendarioMensual = false;
    }

    public function generarDiasDelMes()
    {
        $primerDia = Carbon::create($this->anioActual, $this->mesActual, 1);
        $ultimoDia = $primerDia->copy()->endOfMonth();
        
        $diaSemana = $primerDia->dayOfWeekIso;
        $diasMesAnterior = [];
        if ($diaSemana > 1) {
            $ultimoDiaMesAnterior = $primerDia->copy()->subMonth()->endOfMonth();
            for ($i = $diaSemana - 1; $i > 0; $i--) {
                $diasMesAnterior[] = [
                    'fecha' => $ultimoDiaMesAnterior->copy()->subDays($i - 1),
                    'mesActual' => false,
                ];
            }
        }
        
        $diasMesActual = [];
        for ($dia = 1; $dia <= $ultimoDia->day; $dia++) {
            $diasMesActual[] = [
                'fecha' => Carbon::create($this->anioActual, $this->mesActual, $dia),
                'mesActual' => true,
            ];
        }
        
        $totalDias = count($diasMesAnterior) + count($diasMesActual);
        $diasMesSiguiente = [];
        if ($totalDias < 35) {
            $diasFaltantes = 35 - $totalDias;
            $primerDiaMesSiguiente = $ultimoDia->copy()->addDay();
            for ($i = 0; $i < $diasFaltantes; $i++) {
                $diasMesSiguiente[] = [
                    'fecha' => $primerDiaMesSiguiente->copy()->addDays($i),
                    'mesActual' => false,
                ];
            }
        }
        
        $this->diasDelMes = array_merge($diasMesAnterior, $diasMesActual, $diasMesSiguiente);
        
        $inicioMes = $primerDia->copy()->startOfDay();
        $finMes = $ultimoDia->copy()->endOfDay();
        
        $clases = Clase::whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', 'programada')
            ->with('tipo_entrenamiento')
            ->orderBy('hora_inicio')
            ->get();
        
        $this->clasesPorDia = $clases->groupBy(function ($clase) {
            return $clase->fecha->format('Y-m-d');
        })->map(function ($clasesDelDia) {
            return $clasesDelDia->map(function ($clase) {
                return [
                    'id' => $clase->id,
                    'hora' => $clase->hora_inicio->format('H:i'),
                    'tipo' => $clase->tipo_entrenamiento->nombre ?? 'Clase',
                    'estado' => $clase->estado,
                ];
            })->toArray();
        })->toArray();
    }

    // Métodos del calendario mensual (vista historial)
    public function toggleCalendarioHistorial()
    {
        $this->mostrarCalendarioHistorial = !$this->mostrarCalendarioHistorial;
        if ($this->mostrarCalendarioHistorial) {
            $this->generarDiasDelMesHistorial();
        }
    }

    public function mesAnteriorHistorial()
    {
        $fecha = Carbon::create($this->anioHistorial, $this->mesHistorial, 1)->subMonth();
        $this->mesHistorial = $fecha->month;
        $this->anioHistorial = $fecha->year;
        $this->generarDiasDelMesHistorial();
    }

    public function mesSiguienteHistorial()
    {
        $fecha = Carbon::create($this->anioHistorial, $this->mesHistorial, 1)->addMonth();
        $this->mesHistorial = $fecha->month;
        $this->anioHistorial = $fecha->year;
        $this->generarDiasDelMesHistorial();
    }

    public function irAHoyHistorial()
    {
        $this->mesHistorial = Carbon::now()->month;
        $this->anioHistorial = Carbon::now()->year;
        $this->diaSeleccionado = Carbon::today()->toDateString();
        $this->generarDiasDelMesHistorial();
        $this->cargarHistorial();
    }

    public function irADiaDesdeCalendarioHistorial($fecha)
    {
        $fechaSeleccionada = Carbon::parse($fecha);
        
        // Si la fecha es futura, cambiar a vista disponibles
        if ($fechaSeleccionada->gte(Carbon::today())) {
            $this->vistaActiva = 'disponibles';
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->addDays($i);
            });
        }
        
        $this->diaSeleccionado = $fecha;
        $this->cargarClases();
        $this->cargarHistorial();
        $this->mostrarCalendarioHistorial = false;
    }

    public function generarDiasDelMesHistorial()
    {
        $primerDia = Carbon::create($this->anioHistorial, $this->mesHistorial, 1);
        $ultimoDia = $primerDia->copy()->endOfMonth();
        
        $diaSemana = $primerDia->dayOfWeekIso;
        $diasMesAnterior = [];
        if ($diaSemana > 1) {
            $ultimoDiaMesAnterior = $primerDia->copy()->subMonth()->endOfMonth();
            for ($i = $diaSemana - 1; $i > 0; $i--) {
                $diasMesAnterior[] = [
                    'fecha' => $ultimoDiaMesAnterior->copy()->subDays($i - 1),
                    'mesActual' => false,
                ];
            }
        }
        
        $diasMesActual = [];
        for ($dia = 1; $dia <= $ultimoDia->day; $dia++) {
            $diasMesActual[] = [
                'fecha' => Carbon::create($this->anioHistorial, $this->mesHistorial, $dia),
                'mesActual' => true,
            ];
        }
        
        $totalDias = count($diasMesAnterior) + count($diasMesActual);
        $diasMesSiguiente = [];
        if ($totalDias < 35) {
            $diasFaltantes = 35 - $totalDias;
            $primerDiaMesSiguiente = $ultimoDia->copy()->addDay();
            for ($i = 0; $i < $diasFaltantes; $i++) {
                $diasMesSiguiente[] = [
                    'fecha' => $primerDiaMesSiguiente->copy()->addDays($i),
                    'mesActual' => false,
                ];
            }
        }
        
        $this->diasDelMesHistorial = array_merge($diasMesAnterior, $diasMesActual, $diasMesSiguiente);
        
        $inicioMes = $primerDia->copy()->startOfDay();
        $finMes = $ultimoDia->copy()->endOfDay();
        
        $clases = Clase::whereBetween('fecha', [$inicioMes, $finMes])
            ->whereHas('asistencias', function ($query) {
                $query->where('usuario_id', Auth::id())
                    ->whereIn('estado', ['asistio', 'ausente', 'reservo']);
            })
            ->with('tipo_entrenamiento')
            ->orderBy('hora_inicio')
            ->get();
        
        $this->clasesPorDiaHistorial = $clases->groupBy(function ($clase) {
            return $clase->fecha->format('Y-m-d');
        })->map(function ($clasesDelDia) {
            return $clasesDelDia->map(function ($clase) {
                return [
                    'id' => $clase->id,
                    'hora' => $clase->hora_inicio->format('H:i'),
                    'tipo' => $clase->tipo_entrenamiento->nombre ?? 'Clase',
                    'estado' => $clase->estado,
                ];
            })->toArray();
        })->toArray();
    }

    public function render()
    {
        return view('livewire.athlete.avaliable-classes');
    }
}