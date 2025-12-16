<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\User;
use App\Models\Membresia;
use App\Models\Asistencia;
use App\Models\Clase;
use Carbon\Carbon;
use Livewire\Attributes\On;

class ViewUserModal extends Component
{
    public $usuario;
    public $mostrarModal = false;
    public $estadoMembresia;
    public $colorMembresia;
    public $membresia;
    public $estadisticas = [];

    #[On('open-user-modal')]
    public function openModal($userId)
    {
        $this->usuario = User::with(['role', 'membresias' => function($query) {
            $query->with(['plan', 'tipoEntrenamiento'])->orderBy('fecha_vencimiento', 'desc');
        }])->find($userId);

        if (!$this->usuario) return;

        // Calcular estadísticas según el rol
        if ($this->usuario->role->nombre === 'atleta') {
            $this->calcularEstadisticasAtleta();
            $this->calcularEstadoMembresia();
        } elseif ($this->usuario->role->nombre === 'coach') {
            $this->calcularEstadisticasCoach();
        }

        $this->mostrarModal = true;
    }

    public function calcularEstadoMembresia()
    {
        // Obtener membresía activa más reciente
        $this->membresia = $this->usuario->membresias()
            ->where('estado', 'activa')
            ->orderBy('fecha_vencimiento', 'desc')
            ->first();

        if ($this->membresia) {
            // Verificar si está vencida
            if (Carbon::parse($this->membresia->fecha_vencimiento)->lt(Carbon::today())) {
                $this->estadoMembresia = 'Membresía vencida';
                $this->colorMembresia = 'danger';
            } else {
                $this->estadoMembresia = 'Membresía activa';
                $this->colorMembresia = 'success';
            }
        } else {
            $this->estadoMembresia = 'Sin membresía';
            $this->colorMembresia = 'orange';
        }
    }

    public function calcularEstadisticasAtleta()
    {
        // Total de clases a las que asistió
        $totalAsistencias = Asistencia::where('usuario_id', $this->usuario->id)
            ->where('estado', 'asistio')
            ->count();

        // Clases este mes
        $asistenciasEsteMes = Asistencia::where('usuario_id', $this->usuario->id)
            ->where('estado', 'asistio')
            ->whereHas('clase', function($query) {
                $query->whereMonth('fecha', Carbon::now()->month)
                      ->whereYear('fecha', Carbon::now()->year);
            })
            ->count();

        // Última asistencia
        $ultimaAsistencia = Asistencia::where('usuario_id', $this->usuario->id)
            ->where('estado', 'asistio')
            ->whereHas('clase')
            ->with('clase')
            ->orderBy('created_at', 'desc')
            ->first();

        // Clases reservadas pendientes
        $clasesReservadas = Asistencia::where('usuario_id', $this->usuario->id)
            ->where('estado', 'reservo')
            ->whereHas('clase', function($query) {
                $query->where('fecha', '>=', Carbon::today())
                      ->where('estado', 'programada');
            })
            ->count();

        $this->estadisticas = [
            'total_asistencias' => $totalAsistencias,
            'asistencias_mes' => $asistenciasEsteMes,
            'ultima_asistencia' => $ultimaAsistencia ? Carbon::parse($ultimaAsistencia->clase->fecha)->format('d/m/Y') : 'Nunca',
            'clases_reservadas' => $clasesReservadas,
        ];
    }

    public function calcularEstadisticasCoach()
    {
        // Total de clases impartidas (completadas)
        $totalClases = Clase::where('coach_id', $this->usuario->id)
            ->where('estado', 'completada')
            ->count();

        // Clases impartidas este mes
        $clasesEsteMes = Clase::where('coach_id', $this->usuario->id)
            ->where('estado', 'completada')
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->count();

        // Próxima clase programada
        $proximaClase = Clase::where('coach_id', $this->usuario->id)
            ->where('estado', 'programada')
            ->where('fecha', '>=', Carbon::today())
            ->orderBy('fecha', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->first();

        // Total de atletas únicos que han asistido a sus clases
        $atletasUnicos = Asistencia::whereHas('clase', function($query) {
                $query->where('coach_id', $this->usuario->id)
                      ->where('estado', 'completada');
            })
            ->where('estado', 'asistio')
            ->distinct('usuario_id')
            ->count('usuario_id');

        // Promedio de asistencia (atletas que asistieron vs cupos ocupados)
        $clasesCompletadas = Clase::where('coach_id', $this->usuario->id)
            ->where('estado', 'completada')
            ->get();

        $totalReservados = 0;
        $totalAsistieron = 0;

        foreach ($clasesCompletadas as $clase) {
            $reservados = Asistencia::where('clase_id', $clase->id)
                ->whereIn('estado', ['reservo', 'asistio'])
                ->count();
            
            $asistieron = Asistencia::where('clase_id', $clase->id)
                ->where('estado', 'asistio')
                ->count();

            $totalReservados += $reservados;
            $totalAsistieron += $asistieron;
        }

        $promedioAsistencia = $totalReservados > 0 ? round(($totalAsistieron / $totalReservados) * 100, 1) : 0;

        // Clases canceladas
        $clasesCanceladas = Clase::where('coach_id', $this->usuario->id)
            ->where('estado', 'cancelada')
            ->count();

        $this->estadisticas = [
            'total_clases' => $totalClases,
            'clases_mes' => $clasesEsteMes,
            'proxima_clase' => $proximaClase ? Carbon::parse($proximaClase->fecha)->format('d/m/Y') . ' ' . Carbon::parse($proximaClase->hora_inicio)->format('H:i') : 'Sin clases programadas',
            'atletas_unicos' => $atletasUnicos,
            'promedio_asistencia' => $promedioAsistencia,
            'clases_canceladas' => $clasesCanceladas,
        ];
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->usuario = null;
        $this->membresia = null;
        $this->estadisticas = [];
    }

    public function render()
    {
        return view('livewire.coach.view-user-modal');
    }
}
