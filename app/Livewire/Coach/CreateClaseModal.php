<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\Clase;
use App\Models\TipoEntrenamiento;
use App\Models\User;
use App\Models\Role;
use App\Models\Wod;
use App\Models\Ejercicio;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class CreateClaseModal extends Component
{
    public $mostrarModal = false;
    
    // Campos del formulario
    public $fecha;
    public $fecha_display;
    public $hora_inicio_hora;
    public $hora_inicio_minuto;
    public $hora_fin_hora;
    public $hora_fin_minuto;
    public $tipo_entrenamiento_id;
    public $coach_id;
    public $cupo = 12;
    public $selectedWodId;
    
    // WOD management
    public $wod;
    public $editandoWod = false;
    public $listaEjercicios;
    public $ejerciciosWod = [];
    public $editingExerciseIndex = null;
    public $edit_wod_nombre;
    public $edit_wod_duracion;
    public $edit_wod_descripcion;
    
    // Modal crear ejercicio
    public $mostrarModalCrearEjercicio = false;
    public $nuevo_ejercicio_nombre = '';
    public $nuevo_ejercicio_descripcion = '';
    
    // Listas para selects
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
        $this->listaEjercicios = Ejercicio::orderBy('nombre')->get();
    }

    public function cargarWods()
    {
        $this->wods_disponibles = Wod::orderBy('created_at', 'desc')->take(50)->get();
    }

    public function updatedSelectedWodId($value)
    {
        // Cargar el WOD seleccionado con sus ejercicios
        if ($value) {
            $this->wod = Wod::with('ejercicios')->find($value);
        } else {
            $this->wod = null;
        }
    }

    public function cargarWodSeleccionado()
    {
        // Método público para cargar el WOD cuando cambia el select
        if ($this->selectedWodId) {
            $this->wod = Wod::with('ejercicios')->find($this->selectedWodId);
        } else {
            $this->wod = null;
        }
    }

    public function editarWod()
    {
        if ($this->wod) {
            $this->edit_wod_nombre = $this->wod->nombre;
            $this->edit_wod_duracion = $this->wod->duracion;
            $this->edit_wod_descripcion = $this->wod->descripcion;
            $this->cargarEjerciciosDelWod();
            $this->editandoWod = true;
        }
    }

    public function crearNuevoWod()
    {
        // Crear un nuevo WOD en blanco
        $this->edit_wod_nombre = '';
        $this->edit_wod_duracion = null;
        $this->edit_wod_descripcion = '';
        $this->ejerciciosWod = [];
        $this->agregarEjercicioWod(); // Agregar un ejercicio vacío por defecto
        $this->editandoWod = true;
        $this->wod = null; // No hay WOD seleccionado porque es nuevo
    }

    public function cargarEjerciciosDelWod()
    {
        $this->ejerciciosWod = [];
        if ($this->wod && $this->wod->ejercicios) {
            foreach ($this->wod->ejercicios as $e) {
                $this->ejerciciosWod[] = [
                    'id' => $e->id,
                    'orden' => $e->pivot->orden ?? 0,
                    'series' => $e->pivot->series,
                    'repeticiones' => $e->pivot->repeticiones,
                    'duracion' => $e->pivot->duracion,
                ];
            }
        }
        
        // Si no hay ejercicios, agregar uno vacío
        if (empty($this->ejerciciosWod)) {
            $this->agregarEjercicioWod();
        }
    }

    public function agregarEjercicioWod()
    {
        $nuevoIndex = count($this->ejerciciosWod);
        
        $this->ejerciciosWod[] = [
            'id' => null,
            'orden' => $nuevoIndex + 1,
            'series' => null,
            'repeticiones' => null,
            'duracion' => null,
        ];
        
        // Poner el nuevo ejercicio en modo edición
        $this->editingExerciseIndex = $nuevoIndex;
    }

    public function eliminarEjercicioWod($index)
    {
        unset($this->ejerciciosWod[$index]);
        $this->ejerciciosWod = array_values($this->ejerciciosWod);

        // Reordenar
        foreach ($this->ejerciciosWod as $i => $e) {
            $this->ejerciciosWod[$i]['orden'] = $i + 1;
        }
    }

    public function editarEjercicioWod($index)
    {
        $this->editingExerciseIndex = $index;
    }

    public function guardarEjercicioWod($index)
    {
        $this->editingExerciseIndex = null;
    }

    public function cancelarEdicionEjercicio()
    {
        $this->editingExerciseIndex = null;
    }

    public function moverEjercicioArriba($index)
    {
        if ($index > 0) {
            $temp = $this->ejerciciosWod[$index];
            $this->ejerciciosWod[$index] = $this->ejerciciosWod[$index - 1];
            $this->ejerciciosWod[$index - 1] = $temp;
        }
    }

    public function moverEjercicioAbajo($index)
    {
        if ($index < count($this->ejerciciosWod) - 1) {
            $temp = $this->ejerciciosWod[$index];
            $this->ejerciciosWod[$index] = $this->ejerciciosWod[$index + 1];
            $this->ejerciciosWod[$index + 1] = $temp;
        }
    }

    public function abrirModalCrearEjercicio()
    {
        $this->mostrarModalCrearEjercicio = true;
        $this->nuevo_ejercicio_nombre = '';
        $this->nuevo_ejercicio_descripcion = '';
        $this->resetValidation(['nuevo_ejercicio_nombre']);
    }

    public function cerrarModalCrearEjercicio()
    {
        $this->mostrarModalCrearEjercicio = false;
        $this->nuevo_ejercicio_nombre = '';
        $this->nuevo_ejercicio_descripcion = '';
        $this->resetValidation(['nuevo_ejercicio_nombre']);
    }

    public function crearEjercicio()
    {
        $this->validate([
            'nuevo_ejercicio_nombre' => 'required|string|max:255',
        ], [
            'nuevo_ejercicio_nombre.required' => 'El nombre del ejercicio es obligatorio',
        ]);

        $nuevoEjercicio = Ejercicio::create([
            'nombre' => $this->nuevo_ejercicio_nombre,
            'descripcion' => $this->nuevo_ejercicio_descripcion,
        ]);

        // Recargar lista de ejercicios
        $this->listaEjercicios = Ejercicio::orderBy('nombre')->get();

        // Cerrar modal
        $this->cerrarModalCrearEjercicio();

        // Agregar el nuevo ejercicio automáticamente en modo edición
        $nuevoIndex = count($this->ejerciciosWod);
        
        $this->ejerciciosWod[] = [
            'id' => $nuevoEjercicio->id,
            'orden' => $nuevoIndex + 1,
            'series' => null,
            'repeticiones' => null,
            'duracion' => null,
        ];
        
        // Poner el nuevo ejercicio en modo edición
        $this->editingExerciseIndex = $nuevoIndex;

        session()->flash('message', 'Ejercicio creado y agregado exitosamente');
    }

    public function guardarWod()
    {
        // Validar datos básicos del WOD
        $this->validate([
            'edit_wod_nombre' => 'required|string|max:100',
            'edit_wod_duracion' => 'nullable|integer|min:1',
            'edit_wod_descripcion' => 'nullable|string',
        ]);

        // Si no hay WOD, crear uno nuevo
        if (!$this->wod) {
            $this->wod = Wod::create([
                'nombre' => $this->edit_wod_nombre,
                'duracion' => $this->edit_wod_duracion,
                'descripcion' => $this->edit_wod_descripcion,
                'user_id' => Auth::id(),
                'tipo_entrenamiento_id' => $this->tipo_entrenamiento_id ?? $this->tipos_entrenamiento->first()->id,
            ]);
        } else {
            // Actualizar datos básicos del WOD existente
            $this->wod->update([
                'nombre' => $this->edit_wod_nombre,
                'duracion' => $this->edit_wod_duracion,
                'descripcion' => $this->edit_wod_descripcion,
            ]);
        }

        // Sincronizar ejercicios
        $ejerciciosSync = [];
        foreach ($this->ejerciciosWod as $ej) {
            if ($ej['id']) {
                $ejerciciosSync[$ej['id']] = [
                    'orden' => $ej['orden'],
                    'series' => $ej['series'],
                    'repeticiones' => $ej['repeticiones'],
                    'duracion' => $ej['duracion'],
                ];
            }
        }
        $this->wod->ejercicios()->sync($ejerciciosSync);

        // Recargar WOD con ejercicios actualizados
        $this->wod->refresh();
        $this->wod->load('ejercicios');

        // Seleccionar el WOD recién creado/editado
        $this->selectedWodId = $this->wod->id;

        // Recargar lista de WODs disponibles
        $this->cargarWods();

        $this->editandoWod = false;
    }

    public function cancelarEdicionWod()
    {
        $this->editandoWod = false;
        $this->cargarEjerciciosDelWod();
    }

    #[On('open-create-modal')] 
    public function openModal($fecha = null)
    {
        $this->resetValidation();
        $this->cargarWods();

        // Asignamos el coach logueado por defecto
        $this->coach_id = Auth::id();

        // Si hay tipos de entrenamiento, seleccionamos el primero por defecto
        if ($this->tipos_entrenamiento->isNotEmpty()) {
            $this->tipo_entrenamiento_id = $this->tipos_entrenamiento->first()->id;
        }

        // Si hay WODs disponibles, seleccionamos el primero por defecto
        if ($this->wods_disponibles->isNotEmpty()) {
            $this->selectedWodId = $this->wods_disponibles->first()->id;
            // Cargar el WOD seleccionado con sus ejercicios
            $this->wod = Wod::with('ejercicios')->find($this->selectedWodId);
        }

        // Establecer fecha
        if ($fecha) {
            $this->fecha = $fecha;
            $this->fecha_display = \Carbon\Carbon::parse($fecha)->format('d/m/Y');
        } else {
            $this->fecha = now()->format('Y-m-d');
            $this->fecha_display = now()->format('d/m/Y');
        }
        
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->resetValidation();
        $this->reset([
            'fecha',
            'fecha_display',
            'hora_inicio_hora',
            'hora_inicio_minuto',
            'hora_fin_hora',
            'hora_fin_minuto',
            'tipo_entrenamiento_id',
            'selectedWodId'
        ]);
    }

    public function save()
    {
        // Convertir fecha de DD/MM/AAAA a Y-m-d
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $this->fecha_display, $matches)) {
            $this->fecha = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        } else {
            $this->addError('fecha_display', 'Formato de fecha inválido. Use DD/MM/AAAA.');
            return;
        }

        // Reconstruir las horas en formato HH:MM
        $hora_inicio = sprintf('%02d:%02d', (int)$this->hora_inicio_hora, (int)$this->hora_inicio_minuto);
        $hora_fin = sprintf('%02d:%02d', (int)$this->hora_fin_hora, (int)$this->hora_fin_minuto);

        // Validar fecha antes de las otras validaciones
        if ($this->fecha < date('Y-m-d')) {
            $this->addError('fecha', 'La fecha no puede ser anterior a hoy.');
            return;
        }

        $this->validate([
            'fecha' => 'required|date',
            'hora_inicio_hora' => 'required|numeric|min:0|max:23',
            'hora_inicio_minuto' => 'required|numeric|min:0|max:59',
            'hora_fin_hora' => 'required|numeric|min:0|max:23',
            'hora_fin_minuto' => 'required|numeric|min:0|max:59',
            'tipo_entrenamiento_id' => 'required|exists:tipos_entrenamiento,id',
            'coach_id' => 'required|exists:users,id',
            'cupo' => 'required|integer|min:1',
            'selectedWodId' => 'required|exists:wods,id',
        ], [
            'hora_inicio_hora.required' => 'La hora de inicio es requerida.',
            'hora_inicio_hora.numeric' => 'La hora debe ser un número.',
            'hora_inicio_hora.min' => 'La hora debe ser mayor o igual a 0.',
            'hora_inicio_hora.max' => 'La hora debe ser menor o igual a 23.',
            'hora_inicio_minuto.required' => 'Los minutos de inicio son requeridos.',
            'hora_inicio_minuto.numeric' => 'Los minutos deben ser un número.',
            'hora_inicio_minuto.min' => 'Los minutos deben ser mayor o igual a 0.',
            'hora_inicio_minuto.max' => 'Los minutos deben ser menor o igual a 59.',
            'hora_fin_hora.required' => 'La hora de fin es requerida.',
            'hora_fin_hora.numeric' => 'La hora debe ser un número.',
            'hora_fin_hora.min' => 'La hora debe ser mayor o igual a 0.',
            'hora_fin_hora.max' => 'La hora debe ser menor o igual a 23.',
            'hora_fin_minuto.required' => 'Los minutos de fin son requeridos.',
            'hora_fin_minuto.numeric' => 'Los minutos deben ser un número.',
            'hora_fin_minuto.min' => 'Los minutos deben ser mayor o igual a 0.',
            'hora_fin_minuto.max' => 'Los minutos deben ser menor o igual a 59.',
            'tipo_entrenamiento_id.required' => 'El tipo de entrenamiento es requerido.',
            'selectedWodId.required' => 'Debes seleccionar un WOD.',
        ]);

        // Validar que hora fin sea posterior a hora inicio
        if ($hora_fin <= $hora_inicio) {
            $this->addError('hora_fin', 'La hora de fin debe ser mayor a la hora de inicio.');
            return;
        }

        // Validar que si es hoy, la hora no haya pasado
        if ($this->fecha === date('Y-m-d')) {
            $horaActual = date('H:i');
            if ($hora_inicio < $horaActual) {
                $this->addError('hora_inicio', 'La hora de inicio no puede ser anterior a la hora actual.');
                return;
            }
        }

        // Guardamos
        Clase::create([
            'fecha' => $this->fecha,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin,
            'tipo_entrenamiento_id' => $this->tipo_entrenamiento_id,
            'coach_id' => $this->coach_id,
            'cupo' => $this->cupo,
            'wod_id' => $this->selectedWodId,
            'estado' => 'programada',
        ]);

        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-next-class');
        $this->cerrarModal();
    }

    public function render()
    {
        return view('livewire.coach.create-clase-modal');
    }
}