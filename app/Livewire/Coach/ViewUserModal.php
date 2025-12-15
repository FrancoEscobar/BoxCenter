<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\User;
use App\Models\Membresia;
use App\Models\Asistencia;
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
            $query->orderBy('fecha_vencimiento', 'desc');
        }])->find($userId);

        if (!$this->usuario) return;

        // Calcular estadísticas si es atleta
        if ($this->usuario->role->nombre === 'atleta') {
            $this->calcularEstadisticasAtleta();
            $this->calcularEstadoMembresia();
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
