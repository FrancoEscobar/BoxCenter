<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\Clase;
use App\Models\TipoEntrenamiento;
use App\Models\User;
use App\Models\Role;
use App\Models\Wod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class CreateClaseModal extends Component
{
    // Campos del formulario
    public $fecha;
    public $hora_inicio = '09:00';
    public $hora_fin = '10:00';
    public $tipo_entrenamiento_id;
    public $coach_id;
    public $cupo = 20;
    public $selected_wod_id;
    public $tipos_entrenamiento;
    public $coaches;
    public $wods_disponibles;

    public function mount()
    {
        // Cargamos los datos estáticos una sola vez
        $this->tipos_entrenamiento = TipoEntrenamiento::orderBy('nombre')->get();
        
        $coachRole = Role::where('nombre', 'coach')->first();
        $this->coaches = User::where('rol_id', $coachRole->id)->orderBy('name')->get();
        
        $this->coach_id = Auth::id(); 

        $this->cargarWods();
    }

    public function cargarWods()
    {
        $this->wods_disponibles = Wod::orderBy('created_at', 'desc')->take(50)->get();
    }

    // --- EVENT LISTENERS ---

    // Este método se ejecuta cuando hacemos clic en el calendario o en el botón "Crear"
    #[On('open-create-modal')] 
    public function openModal($fecha = null)
    {
        $this->resetValidation();
        $this->reset(['selected_wod_id']);
        $this->cargarWods();

        // Asignamos el coach logueado por defecto
        $this->coach_id = Auth::id();

        // Si hay tipos de entrenamiento, seleccionamos el primero por defecto
        if ($this->tipos_entrenamiento->isNotEmpty()) {
            $this->tipo_entrenamiento_id = $this->tipos_entrenamiento->first()->id;
        }

        // Si hay WODs disponibles, seleccionamos el primero por defecto
        if ($this->wods_disponibles->isNotEmpty()) {
            $this->selected_wod_id = $this->wods_disponibles->first()->id;
        }

        // Si se pasa un array con 'fecha', usamos ese valor; si no, usamos la fecha actual
        if (is_array($fecha) && isset($fecha['fecha'])) {
            $this->fecha = $fecha['fecha'];
        } else {
            $this->fecha = $fecha ?? now()->format('Y-m-d');
        }
        
        $this->dispatch('update-modal-date', date: $this->fecha);

        $this->dispatch('show-bootstrap-modal');
    }

public function save()
    {
        // Definimos las reglas
        $rules = [
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'tipo_entrenamiento_id' => 'required|exists:tipos_entrenamiento,id',
            'coach_id' => 'required|exists:users,id',
            'cupo' => 'required|integer|min:1',
            'selected_wod_id' => 'required|exists:wods,id',
        ];

        // Definimos los mensajes personalizados
        $messages = [
            'tipo_entrenamiento_id.required' => 'Por favor, selecciona el tipo de entrenamiento.',
            'selected_wod_id.required' => 'Es obligatorio elegir una Rutina (WOD) o crear una nueva.',
            'coach_id.required' => 'Debes asignar un Coach a la clase.',
            'hora_fin.after' => 'La hora de fin debe ser posterior al inicio.',
        ];

        // Definimos los nombres de los atributos (para los errores genéricos)
        $attributes = [
            'fecha' => 'fecha',
            'hora_inicio' => 'hora de inicio',
            'hora_fin' => 'hora de fin',
            'tipo_entrenamiento_id' => 'tipo de entrenamiento',
            'coach_id' => 'coach',
            'cupo' => 'cupo',
            'selected_wod_id' => 'rutina (WOD)',
        ];

        // Validamos explícitamente pasando los 3 arrays
        $this->validate($rules, $messages, $attributes);

        // Guardamos
        Clase::create([
            'fecha' => $this->fecha,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'tipo_entrenamiento_id' => $this->tipo_entrenamiento_id,
            'coach_id' => $this->coach_id,
            'cupo' => $this->cupo,
            'wod_id' => $this->selected_wod_id,
            'estado' => 'programada',
        ]);

        $this->dispatch('hide-bootstrap-modal');
        $this->dispatch('refresh-calendar');
        $this->reset(['hora_inicio', 'hora_fin', 'tipo_entrenamiento_id', 'selected_wod_id']);
    }

    public function render()
    {
        return view('livewire.coach.create-clase-modal');
    }
}