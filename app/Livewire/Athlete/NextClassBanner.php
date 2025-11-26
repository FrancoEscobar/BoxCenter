<?php

namespace App\Livewire\Athlete;

use Livewire\Component;
use App\Models\Asistencia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\On;

class NextClassBanner extends Component
{
    public $proximaClase = null;

    public function mount()
    {
        $this->cargarProximaClase();
    }

    #[On('reserva-actualizada')]
    public function cargarProximaClase()
    {
        $this->proximaClase = Asistencia::where('usuario_id', Auth::id())
            ->whereIn('estado', ['reservo', 'ausente'])
            ->whereHas('clase', function ($query) {
                $query->where('estado', 'programada')
                      ->where('fecha', '>=', Carbon::today());
            })
            ->with(['clase.tipo_entrenamiento', 'clase.coach'])
            ->orderBy('created_at')
            ->first();
    }

    public function render()
    {
        return view('livewire.athlete.next-class-banner');
    }
}
