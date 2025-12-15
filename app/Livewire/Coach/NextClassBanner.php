<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\Clase;
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

    #[On('refresh-next-class')]
    public function cargarProximaClase()
    {
        $this->proximaClase = Clase::where('coach_id', Auth::id())
            ->where('estado', 'programada')
            ->where('fecha', '>=', Carbon::today())
            ->with('tipo_entrenamiento')
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->first();
    }

    public function abrirDetalles()
    {
        if ($this->proximaClase) {
            $this->dispatch('open-view-modal', claseId: $this->proximaClase->id);
        }
    }

    public function render()
    {
        return view('livewire.coach.next-class-banner');
    }
}
