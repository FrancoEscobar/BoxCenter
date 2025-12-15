<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Clase;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class MonthlyCalendar extends Component
{
    public $mesActual;
    public $anioActual;
    public $diasDelMes = [];
    public $clasesPorDia = [];
    public $mostrarCalendario = false;

    public function mount()
    {
        $this->mesActual = Carbon::now()->month;
        $this->anioActual = Carbon::now()->year;
        $this->cargarCalendario();
    }

    #[On('refresh-calendar')]
    public function refreshCalendar()
    {
        $this->cargarCalendario();
    }

    public function mesAnterior()
    {
        $fecha = Carbon::createFromDate($this->anioActual, $this->mesActual, 1)->subMonth();
        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;
        $this->cargarCalendario();
    }

    public function mesSiguiente()
    {
        $fecha = Carbon::createFromDate($this->anioActual, $this->mesActual, 1)->addMonth();
        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;
        $this->cargarCalendario();
    }

    public function irAHoy()
    {
        $this->mesActual = Carbon::now()->month;
        $this->anioActual = Carbon::now()->year;
        $this->cargarCalendario();
    }

    public function toggleCalendario()
    {
        $this->mostrarCalendario = !$this->mostrarCalendario;
    }

    public function cargarCalendario()
    {
        $primerDia = Carbon::createFromDate($this->anioActual, $this->mesActual, 1);
        $ultimoDia = $primerDia->copy()->endOfMonth();
        
        // Obtener el primer día de la semana (lunes = 1, domingo = 0)
        $primerDiaSemana = $primerDia->dayOfWeekIso; // Lunes = 1
        
        // Días del mes anterior para completar la primera semana
        $diasMesAnterior = [];
        $ultimoDiaMesAnterior = $primerDia->copy()->subDay();
        for ($i = $primerDiaSemana - 1; $i > 0; $i--) {
            $diasMesAnterior[] = [
                'fecha' => $ultimoDiaMesAnterior->copy()->subDays($i - 1),
                'mesActual' => false,
            ];
        }
        
        // Días del mes actual
        $diasMesActual = [];
        for ($dia = 1; $dia <= $ultimoDia->day; $dia++) {
            $fecha = Carbon::createFromDate($this->anioActual, $this->mesActual, $dia);
            $diasMesActual[] = [
                'fecha' => $fecha,
                'mesActual' => true,
            ];
        }
        
        // Días del mes siguiente para completar la última semana
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
        
        // Cargar clases del mes
        $inicioMes = $primerDia->copy()->startOfDay();
        $finMes = $ultimoDia->copy()->endOfDay();
        
        $clases = Clase::where('coach_id', Auth::id())
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->whereIn('estado', ['programada', 'cancelada', 'realizada'])
            ->with('tipo_entrenamiento')
            ->orderBy('hora_inicio')
            ->get();
        
        // Agrupar clases por día
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
            });
        })->toArray();
    }

    public function abrirClase($claseId)
    {
        $this->dispatch('open-view-modal', claseId: $claseId, desdeHistorial: false);
    }

    public function render()
    {
        return view('livewire.coach.monthly-calendar');
    }
}
