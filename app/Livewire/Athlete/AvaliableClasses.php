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
    public $claseSeleccionada = null;

    public function mount()
    {
        // Próximos 60 días incluyendo hoy
        $this->dias = collect(range(0, 59))->map(function ($i) {
            return Carbon::today()->addDays($i);
        });

        $this->diaSeleccionado = Carbon::today()->toDateString();
        $this->cargarClases();
    }

    public function cambiarDia($fecha)
    {
        $this->diaSeleccionado = $fecha;
        $this->cargarClases();
    }

    public function abrirModal($claseId)
    {
        $clase = collect($this->clasesDelDia)->firstWhere('id', $claseId);
        $this->claseSeleccionada = $clase;
        $this->dispatch('abrir-modal');
    }

    public function cerrarModal()
    {
        $this->claseSeleccionada = null;
        $this->dispatch('cerrar-modal');
    }

    public function cargarClases()
    {
        $clases = Clase::whereDate('fecha', $this->diaSeleccionado)
            ->where('estado', 'programada')
            ->with(['tipo_entrenamiento', 'coach'])
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
            ];
        })->toArray();
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

    public function render()
    {
        return view('livewire.athlete.avaliable-classes');
    }
}