<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Clase;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class MyclassesSchedule extends Component
{
    public $dias = [];
    public $diaSeleccionado;
    public $clasesDelDia = [];
    public $clasesHistorial = [];
    public $claseSeleccionada = null;
    public $vistaActiva = 'mis-clases'; // 'mis-clases' o 'historial' o 'miembros'
    public $miembros = [];
    public $filtroMiembros = 'todos'; // 'todos', 'coaches', 'atletas'
    public $busquedaMiembros = '';
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
    }

    public function cambiarVista($vista)
    {
        $this->vistaActiva = $vista;
        
        // Cargar días según la vista
        if ($vista === 'mis-clases') {
            // Próximos 60 días incluyendo hoy
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->addDays($i);
            });
            $this->diaSeleccionado = Carbon::today()->toDateString();
            $this->cargarClases();
            $this->cargarHistorial();
        } elseif ($vista === 'historial') {
            // Últimos 60 días incluyendo hoy (de antiguo a reciente, scrolleando hacia atrás)
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->subDays(59 - $i);
            });
            $this->diaSeleccionado = Carbon::today()->toDateString();
            $this->cargarClases();
            $this->cargarHistorial();
        } elseif ($vista === 'miembros') {
            $this->cargarMiembros();
        }
    }

    #[On('refresh-calendar')]
    public function refreshCalendar()
    {
        // Recargar días
        $this->dias = collect(range(0, 59))->map(function ($i) {
            return Carbon::today()->addDays($i);
        });
        
        $this->cargarClases();
    }

    public function cambiarDia($fecha)
    {
        $this->diaSeleccionado = $fecha;
        
        // Recargar días según la vista activa
        if ($this->vistaActiva === 'mis-clases') {
            // Próximos 60 días incluyendo hoy
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->addDays($i);
            });
        } else {
            // Últimos 60 días incluyendo hoy (de antiguo a reciente, scrolleando hacia atrás)
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->subDays(59 - $i);
            });
        }
        
        $this->cargarClases();
        $this->cargarHistorial();
    }

    public function abrirDetalles($claseId)
    {
        $this->dispatch('open-view-modal', claseId: $claseId, desdeHistorial: $this->vistaActiva === 'historial');
    }

    public function abrirCrearClase()
    {
        $this->dispatch('open-create-modal', fecha: $this->diaSeleccionado);
    }

    public function cargarClases()
    {
        $clases = Clase::where('coach_id', Auth::id())
            ->whereDate('fecha', $this->diaSeleccionado)
            ->whereIn('estado', ['programada', 'cancelada'])
            ->with(['tipo_entrenamiento', 'coach'])
            ->withCount(['cuposOcupados' => function ($query) {
                $query->where('estado', '!=', 'cancelo');
            }])
            ->orderBy('hora_inicio')
            ->get();

        $this->clasesDelDia = $clases->map(function ($clase) {
            $cuposDisponibles = $clase->cupo - $clase->cupos_ocupados_count;
            
            return (object)[
                'id' => $clase->id,
                'hora_inicio' => $clase->hora_inicio->format('H:i'),
                'hora_fin' => $clase->hora_fin->format('H:i'),
                'tipo' => $clase->tipo_entrenamiento->nombre ?? 'Clase',
                'cupos' => $cuposDisponibles,
                'cupo_total' => $clase->cupo,
                'inscritos' => $clase->cupos_ocupados_count,
                'estado' => $clase->estado,
                'coach_nombre' => $clase->coach->name ?? 'Sin asignar',
            ];
        })->toArray();
    }

    public function cargarHistorial()
    {
        $clases = Clase::where('coach_id', Auth::id())
            ->whereDate('fecha', $this->diaSeleccionado)
            ->where('fecha', '<', Carbon::today())
            ->with(['tipo_entrenamiento', 'coach'])
            ->withCount(['cuposOcupados' => function ($query) {
                $query->where('estado', '!=', 'cancelo');
            }])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();

        $this->clasesHistorial = $clases->map(function ($clase) {
            $cuposDisponibles = $clase->cupo - $clase->cupos_ocupados_count;
            
            return (object)[
                'id' => $clase->id,
                'fecha' => $clase->fecha->format('d/m/Y'),
                'hora_inicio' => $clase->hora_inicio->format('H:i'),
                'hora_fin' => $clase->hora_fin->format('H:i'),
                'tipo' => $clase->tipo_entrenamiento->nombre ?? 'Clase',
                'cupos' => $cuposDisponibles,
                'cupo_total' => $clase->cupo,
                'inscritos' => $clase->cupos_ocupados_count,
                'estado' => $clase->estado,
                'coach_nombre' => $clase->coach->name ?? 'Sin asignar',
            ];
        })->toArray();
    }

    public function cambiarFiltroMiembros($filtro)
    {
        $this->filtroMiembros = $filtro;
        $this->cargarMiembros();
    }

    public function updatedBusquedaMiembros()
    {
        $this->cargarMiembros();
    }

    public function abrirUsuario($userId)
    {
        $this->dispatch('open-user-modal', userId: $userId);
    }

    public function cargarMiembros()
    {
        $query = \App\Models\User::with('role')
            ->whereHas('role', function ($query) {
                $query->whereIn('nombre', ['atleta', 'coach']);
            });

        // Aplicar filtro según selección
        if ($this->filtroMiembros === 'coaches') {
            $query->whereHas('role', function ($q) {
                $q->where('nombre', 'coach');
            });
        } elseif ($this->filtroMiembros === 'atletas') {
            $query->whereHas('role', function ($q) {
                $q->where('nombre', 'atleta');
            });
        }

        // Aplicar búsqueda si hay término
        if (!empty($this->busquedaMiembros)) {
            $busqueda = $this->busquedaMiembros;
            $query->where(function ($q) use ($busqueda) {
                $q->where('name', 'like', '%' . $busqueda . '%')
                  ->orWhere('apellido', 'like', '%' . $busqueda . '%')
                  ->orWhere('email', 'like', '%' . $busqueda . '%');
            });
        }

        $usuarios = $query->orderBy('name')->get();

        $this->miembros = $usuarios->map(function ($usuario) {
            $estadoMembresia = null;
            $colorMembresia = null;
            
            if ($usuario->role->nombre === 'atleta') {
                // Obtener membresía activa más reciente
                $membresia = $usuario->membresias()
                    ->where('estado', 'activa')
                    ->orderBy('fecha_vencimiento', 'desc')
                    ->first();

                if ($membresia) {
                    // Verificar si está vencida
                    if (Carbon::parse($membresia->fecha_vencimiento)->lt(Carbon::today())) {
                        $estadoMembresia = 'Membresía vencida';
                        $colorMembresia = 'danger';
                    } else {
                        $estadoMembresia = 'Membresía activa';
                        $colorMembresia = 'success';
                    }
                } else {
                    $estadoMembresia = 'Sin membresía';
                    $colorMembresia = 'orange';
                }
            }

            return [
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'rol' => $usuario->role->nombre ?? 'Sin rol',
                'foto_perfil' => $usuario->foto_perfil,
                'estado_membresia' => $estadoMembresia,
                'color_membresia' => $colorMembresia,
            ];
        })->toArray();
    }

    public function toggleCalendarioMensual()
    {
        $this->mostrarCalendarioMensual = !$this->mostrarCalendarioMensual;
        if ($this->mostrarCalendarioMensual) {
            $this->cargarCalendarioMensual();
        }
    }

    public function toggleCalendarioHistorial()
    {
        $this->mostrarCalendarioHistorial = !$this->mostrarCalendarioHistorial;
        if ($this->mostrarCalendarioHistorial) {
            $this->cargarCalendarioHistorial();
        }
    }

    public function mesAnterior()
    {
        $fecha = Carbon::createFromDate($this->anioActual, $this->mesActual, 1)->subMonth();
        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;
        $this->cargarCalendarioMensual();
    }

    public function mesSiguiente()
    {
        $fecha = Carbon::createFromDate($this->anioActual, $this->mesActual, 1)->addMonth();
        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;
        $this->cargarCalendarioMensual();
    }

    public function irAHoyMensual()
    {
        $this->mesActual = Carbon::now()->month;
        $this->anioActual = Carbon::now()->year;
        $this->cargarCalendarioMensual();
    }

    public function mesAnteriorHistorial()
    {
        $fecha = Carbon::createFromDate($this->anioHistorial, $this->mesHistorial, 1)->subMonth();
        $this->mesHistorial = $fecha->month;
        $this->anioHistorial = $fecha->year;
        $this->cargarCalendarioHistorial();
    }

    public function mesSiguienteHistorial()
    {
        $fecha = Carbon::createFromDate($this->anioHistorial, $this->mesHistorial, 1)->addMonth();
        $this->mesHistorial = $fecha->month;
        $this->anioHistorial = $fecha->year;
        $this->cargarCalendarioHistorial();
    }

    public function irAHoyHistorial()
    {
        $this->mesHistorial = Carbon::now()->month;
        $this->anioHistorial = Carbon::now()->year;
        $this->cargarCalendarioHistorial();
    }

    public function cargarCalendarioMensual()
    {
        $primerDia = Carbon::createFromDate($this->anioActual, $this->mesActual, 1);
        $ultimoDia = $primerDia->copy()->endOfMonth();
        
        $primerDiaSemana = $primerDia->dayOfWeekIso;
        
        $diasMesAnterior = [];
        $ultimoDiaMesAnterior = $primerDia->copy()->subDay();
        for ($i = $primerDiaSemana - 1; $i > 0; $i--) {
            $diasMesAnterior[] = [
                'fecha' => $ultimoDiaMesAnterior->copy()->subDays($i - 1),
                'mesActual' => false,
            ];
        }
        
        $diasMesActual = [];
        for ($dia = 1; $dia <= $ultimoDia->day; $dia++) {
            $fecha = Carbon::createFromDate($this->anioActual, $this->mesActual, $dia);
            $diasMesActual[] = [
                'fecha' => $fecha,
                'mesActual' => true,
            ];
        }
        
        $diasMesSiguiente = [];
        $totalDias = count($diasMesAnterior) + count($diasMesActual);
        $diasRestantes = 7 - ($totalDias % 7);
        if ($diasRestantes < 7) {
            $primerDiaMesSiguiente = $ultimoDia->copy()->addDay();
            for ($i = 0; $i < $diasRestantes; $i++) {
                $diasMesSiguiente[] = [
                    'fecha' => $primerDiaMesSiguiente->copy()->addDays($i),
                    'mesActual' => false,
                ];
            }
        }
        
        $this->diasDelMes = array_merge($diasMesAnterior, $diasMesActual, $diasMesSiguiente);
        
        $inicioMes = $primerDia->copy()->startOfDay();
        $finMes = $ultimoDia->copy()->endOfDay();
        
        $clases = Clase::where('coach_id', Auth::id())
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->whereIn('estado', ['programada', 'cancelada', 'realizada'])
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

    public function irADiaDesdeCalendario($fecha)
    {
        $fechaCarbon = Carbon::parse($fecha);
        $this->diaSeleccionado = $fecha;
        $this->mostrarCalendarioMensual = false;
        
        // Si la fecha es pasada, cambiar a vista historial
        if ($fechaCarbon->lt(Carbon::today())) {
            $this->vistaActiva = 'historial';
            // Recargar días para historial
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->subDays(59 - $i);
            });
            $this->cargarHistorial();
        } else {
            $this->cargarClases();
        }
    }

    public function irADiaDesdeCalendarioHistorial($fecha)
    {
        $fechaCarbon = Carbon::parse($fecha);
        $this->diaSeleccionado = $fecha;
        $this->mostrarCalendarioHistorial = false;
        
        // Si la fecha es futura o hoy, cambiar a vista mis-clases
        if ($fechaCarbon->gte(Carbon::today())) {
            $this->vistaActiva = 'mis-clases';
            // Recargar días para mis-clases
            $this->dias = collect(range(0, 59))->map(function ($i) {
                return Carbon::today()->addDays($i);
            });
            $this->cargarClases();
        } else {
            $this->cargarHistorial();
        }
    }

    public function cargarCalendarioHistorial()
    {
        $primerDia = Carbon::createFromDate($this->anioHistorial, $this->mesHistorial, 1);
        $ultimoDia = $primerDia->copy()->endOfMonth();
        
        $primerDiaSemana = $primerDia->dayOfWeekIso;
        
        $diasMesAnterior = [];
        $ultimoDiaMesAnterior = $primerDia->copy()->subDay();
        for ($i = $primerDiaSemana - 1; $i > 0; $i--) {
            $diasMesAnterior[] = [
                'fecha' => $ultimoDiaMesAnterior->copy()->subDays($i - 1),
                'mesActual' => false,
            ];
        }
        
        $diasMesActual = [];
        for ($dia = 1; $dia <= $ultimoDia->day; $dia++) {
            $fecha = Carbon::createFromDate($this->anioHistorial, $this->mesHistorial, $dia);
            $diasMesActual[] = [
                'fecha' => $fecha,
                'mesActual' => true,
            ];
        }
        
        $diasMesSiguiente = [];
        $totalDias = count($diasMesAnterior) + count($diasMesActual);
        $diasRestantes = 7 - ($totalDias % 7);
        if ($diasRestantes < 7) {
            $primerDiaMesSiguiente = $ultimoDia->copy()->addDay();
            for ($i = 0; $i < $diasRestantes; $i++) {
                $diasMesSiguiente[] = [
                    'fecha' => $primerDiaMesSiguiente->copy()->addDays($i),
                    'mesActual' => false,
                ];
            }
        }
        
        $this->diasDelMesHistorial = array_merge($diasMesAnterior, $diasMesActual, $diasMesSiguiente);
        
        $inicioMes = $primerDia->copy()->startOfDay();
        $finMes = $ultimoDia->copy()->endOfDay();
        
        $clases = Clase::where('coach_id', Auth::id())
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->whereIn('estado', ['programada', 'cancelada', 'realizada'])
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
        return view('livewire.coach.myclasses-schedule');
    }
}
