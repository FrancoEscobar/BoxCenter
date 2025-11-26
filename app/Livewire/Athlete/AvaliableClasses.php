<?php

namespace App\Livewire\Athlete;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Clase;

class AvaliableClasses extends Component
{
    public $dias = [];
    public $todasLasClases = [];

    public function mount()
    {
        // Próximos 60 días incluyendo hoy (~2 meses)
        $this->dias = collect(range(0, 59))->map(function ($i) {
            return Carbon::today()->addDays($i);
        });

        // Cargar TODAS las clases de los próximos 60 días de una vez
        $fechaInicio = Carbon::today();
        $fechaFin = Carbon::today()->addDays(59);

        $clases = Clase::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'programada')
            ->with(['tipo_entrenamiento', 'coach'])
            ->withCount(['cuposOcupados' => function ($query) {
                $query->where('estado', '!=', 'cancelo');
            }])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        // Agrupar clases por fecha
        $this->todasLasClases = $clases->groupBy(function ($clase) {
            return Carbon::parse($clase->fecha)->toDateString();
        })->map(function ($clasesDelDia) {
            return $clasesDelDia->map(function ($clase) {
                $cuposDisponibles = $clase->cupo - $clase->cupos_ocupados_count;
                
                return [
                    'id' => $clase->id,
                    'hora_inicio' => $clase->hora_inicio->format('H:i'),
                    'hora_fin' => $clase->hora_fin->format('H:i'),
                    'tipo' => $clase->tipo_entrenamiento->nombre ?? 'Clase',
                    'coach' => $clase->coach->name ?? 'Sin asignar',
                    'cupos' => $cuposDisponibles,
                    'cupo_total' => $clase->cupo,
                    'reservada' => false,
                ];
            })->values();
        })->toArray();
    }

    public function reservarClase($claseId)
    {
        // Simulación de reserva
        session()->flash('success', 'Clase reservada correctamente.');
    }

    public function render()
    {
        return view('livewire.athlete.avaliable-classes');
    }
}