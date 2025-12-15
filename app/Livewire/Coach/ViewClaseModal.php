<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\Clase;
use App\Models\User;
use App\Models\Role;
use App\Models\Wod;
use App\Models\Ejercicio;
use Livewire\Attributes\On;
use App\Events\ClaseCancelada;
use App\Notifications\ClaseCanceladaNotification;
use Illuminate\Support\Facades\Notification;


class ViewClaseModal extends Component
{
    public $clase;
    public $wod;
    public $inscriptos;
    public $showUsers = false; // Toggle para mostrar/ocultar lista de usuarios
    public $mostrarModal = false; // Estado del modal visible
    public $desdeHistorial = false; // Indica si el modal se abrió desde el historial

    public $isEditing = false; // Indicador de modo edición
    public $editandoWod = false; // Modo edición de WOD
    public $coaches; // Lista de coaches para el select
    public $tipos_entrenamiento; // Lista de tipos de entrenamiento
    public $wods; // Lista de WODs disponibles
    public $selectedWodId; // WOD seleccionado en dropdown
    public $listaEjercicios; // Lista de ejercicios disponibles
    public $ejerciciosWod = []; // Ejercicios del WOD en edición
    public $editingExerciseIndex = null; // Índice del ejercicio en edición

    // Modal crear ejercicio
    public $mostrarModalCrearEjercicio = false;
    public $nuevo_ejercicio_nombre = '';
    public $nuevo_ejercicio_descripcion = '';

    // Campos temporales para editar
    public $edit_fecha;
    public $edit_fecha_display;
    public $edit_hora_inicio;
    public $edit_hora_fin;
    public $edit_hora_inicio_hora;
    public $edit_hora_inicio_minuto;
    public $edit_hora_fin_hora;
    public $edit_hora_fin_minuto;
    public $edit_tipo_entrenamiento_id;
    public $edit_coach_id;
    public $edit_cupo;
    // WOD edición
    public $edit_wod_nombre;
    public $edit_wod_duracion;
    public $edit_wod_descripcion;
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
                'user_id' => auth()->id(),
                'tipo_entrenamiento_id' => $this->clase->tipo_entrenamiento_id,
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
        $this->wods = Wod::with('ejercicios')->orderBy('nombre')->get();

        $this->editandoWod = false;
    }

    public function cancelarEdicionWod()
    {
        $this->editandoWod = false;
        $this->cargarEjerciciosDelWod();
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

    public function updatedSelectedWodId($value)
    {
        // Cargar el WOD seleccionado con sus ejercicios
        if ($value) {
            $this->wod = Wod::with('ejercicios')->find($value);
        } else {
            $this->wod = null;
        }
    }

    #[On('open-view-modal')]
    public function openModal($claseId, $desdeHistorial = false)
    {
        $this->desdeHistorial = $desdeHistorial;
        
        // Cargamos la clase con todas las relaciones necesarias
        $this->clase = Clase::with([
            'tipo_entrenamiento', 
            'coach', 
            'wod.ejercicios',
            'asistencias.usuario' // Para obtener la lista de inscriptos
        ])->find($claseId);

        if (!$this->clase) return;

        $this->wod = $this->clase->wod;
        $this->selectedWodId = $this->wod?->id;
        
        $this->inscriptos = $this->clase->asistencias()->with('usuario')->where('estado', '!=', 'cancelo')->get();
        $this->showUsers = false; // Asegurar que la lista de usuarios esté oculta al abrir
        $this->isEditing = false; // Asegurar que no esté en modo edición al abrir
        $this->mostrarModal = true; // Mostrar el modal
        
        // Cargar lista de WODs disponibles
        $this->wods = Wod::with('ejercicios')->orderBy('nombre')->get();
        
        // Cargar lista de ejercicios disponibles
        $this->listaEjercicios = Ejercicio::orderBy('nombre')->get();
        
        // Cargar lista de coaches para el select
        $coachRole = Role::where('nombre', 'coach')->first();
        $this->coaches = User::where('rol_id', $coachRole->id)->orderBy('name')->get();
        
        // Cargar lista de tipos de entrenamiento
        $this->tipos_entrenamiento = \App\Models\TipoEntrenamiento::orderBy('nombre')->get();
        
        // Separar horas y minutos para edición
        if ($this->clase->hora_inicio) {
            $horaInicio = explode(':', $this->clase->hora_inicio);
            $this->edit_hora_inicio_hora = (int)$horaInicio[0];
            $this->edit_hora_inicio_minuto = (int)$horaInicio[1];
        }
        if ($this->clase->hora_fin) {
            $horaFin = explode(':', $this->clase->hora_fin);
            $this->edit_hora_fin_hora = (int)$horaFin[0];
            $this->edit_hora_fin_minuto = (int)$horaFin[1];
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->isEditing = false;
        $this->editandoWod = false;
        $this->editingExerciseIndex = null;
        $this->showUsers = false;
        $this->resetValidation();
        $this->clase = null;
    }

    public function cerrarSinGuardar()
    {
        // Método específico para cerrar sin guardar cuando está editando
        $this->cerrarModal();
    }

    // Activar modo edición y llenar los inputs con los datos actuales
    public function startEditing()
    {
        $this->edit_fecha       = $this->clase->fecha->format('Y-m-d');
        $this->edit_fecha_display = $this->clase->fecha->format('d/m/Y');
        $this->edit_hora_inicio = $this->clase->hora_inicio->format('H:i');
        $this->edit_hora_fin    = $this->clase->hora_fin->format('H:i');
        
        // Separar horas y minutos
        $horaInicio = explode(':', $this->edit_hora_inicio);
        $this->edit_hora_inicio_hora = (int)$horaInicio[0];
        $this->edit_hora_inicio_minuto = (int)$horaInicio[1];
        
        $horaFin = explode(':', $this->edit_hora_fin);
        $this->edit_hora_fin_hora = (int)$horaFin[0];
        $this->edit_hora_fin_minuto = (int)$horaFin[1];
        
        $this->edit_tipo_entrenamiento_id = $this->clase->tipo_entrenamiento_id;
        $this->edit_coach_id    = $this->clase->coach_id;
        $this->edit_cupo        = $this->clase->cupo;
        
        $this->isEditing = true;

        $this->dispatch('initialize-edit-date', date: $this->edit_fecha);
    }

    // Cancelar edición
    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->resetValidation();
    }

    // Guardar cambios de la clase
    public function updateClase()
    {
        // Convertir fecha de DD/MM/AAAA a Y-m-d
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $this->edit_fecha_display, $matches)) {
            $this->edit_fecha = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        } else {
            $this->addError('edit_fecha_display', 'Formato de fecha inválido. Use DD/MM/AAAA.');
            return;
        }
        
        // Reconstruir las horas en formato HH:MM
        $this->edit_hora_inicio = sprintf('%02d:%02d', (int)$this->edit_hora_inicio_hora, (int)$this->edit_hora_inicio_minuto);
        $this->edit_hora_fin = sprintf('%02d:%02d', (int)$this->edit_hora_fin_hora, (int)$this->edit_hora_fin_minuto);
        
        // Validar fecha antes de las otras validaciones
        if ($this->edit_fecha < date('Y-m-d')) {
            $this->addError('edit_fecha', 'La fecha no puede ser anterior a hoy.');
            return;
        }
        
        $this->validate([
            'edit_fecha'       => 'required|date',
            'edit_hora_inicio_hora' => 'required|numeric|min:0|max:23',
            'edit_hora_inicio_minuto' => 'required|numeric|min:0|max:59',
            'edit_hora_fin_hora' => 'required|numeric|min:0|max:23',
            'edit_hora_fin_minuto' => 'required|numeric|min:0|max:59',
            'edit_hora_inicio' => 'required',
            'edit_hora_fin'    => 'required',
            'edit_tipo_entrenamiento_id' => 'required|exists:tipos_entrenamiento,id',
            'edit_coach_id'    => 'required|exists:users,id',
            'edit_cupo'        => 'required|integer|min:1',
        ], [
            'edit_hora_inicio_hora.required' => 'La hora de inicio es requerida.',
            'edit_hora_inicio_hora.numeric' => 'La hora debe ser un número.',
            'edit_hora_inicio_hora.min' => 'La hora debe ser mayor o igual a 0.',
            'edit_hora_inicio_hora.max' => 'La hora debe ser menor o igual a 23.',
            'edit_hora_inicio_minuto.required' => 'Los minutos de inicio son requeridos.',
            'edit_hora_inicio_minuto.numeric' => 'Los minutos deben ser un número.',
            'edit_hora_inicio_minuto.min' => 'Los minutos deben ser mayor o igual a 0.',
            'edit_hora_inicio_minuto.max' => 'Los minutos deben ser menor o igual a 59.',
            'edit_hora_fin_hora.required' => 'La hora de fin es requerida.',
            'edit_hora_fin_hora.numeric' => 'La hora debe ser un número.',
            'edit_hora_fin_hora.min' => 'La hora debe ser mayor o igual a 0.',
            'edit_hora_fin_hora.max' => 'La hora debe ser menor o igual a 23.',
            'edit_hora_fin_minuto.required' => 'Los minutos de fin son requeridos.',
            'edit_hora_fin_minuto.numeric' => 'Los minutos deben ser un número.',
            'edit_hora_fin_minuto.min' => 'Los minutos deben ser mayor o igual a 0.',
            'edit_hora_fin_minuto.max' => 'Los minutos deben ser menor o igual a 59.',
        ]);
        
        // Validar que hora fin sea posterior a hora inicio
        if ($this->edit_hora_fin <= $this->edit_hora_inicio) {
            $this->addError('edit_hora_fin', 'La hora de fin debe ser mayor a la hora de inicio.');
            return;
        }
        
        // Validar que si es hoy, la hora no haya pasado
        if ($this->edit_fecha === date('Y-m-d')) {
            $horaActual = date('H:i');
            if ($this->edit_hora_inicio < $horaActual) {
                $this->addError('edit_hora_inicio', 'La hora de inicio no puede ser anterior a la hora actual.');
                return;
            }
        }

        $this->clase->update([
            'fecha'       => $this->edit_fecha,
            'hora_inicio' => $this->edit_hora_inicio,
            'hora_fin'    => $this->edit_hora_fin,
            'tipo_entrenamiento_id' => $this->edit_tipo_entrenamiento_id,
            'coach_id'    => $this->edit_coach_id,
            'cupo'        => $this->edit_cupo,
            'wod_id'      => $this->selectedWodId,
        ]);

        $this->isEditing = false;
        $this->dispatch('refresh-calendar'); // Refrescar calendario
        $this->dispatch('refresh-next-class'); // Refrescar banner de próxima clase
        
        // Recargar modelo para ver cambios en el modal sin cerrar
        $this->clase->refresh(); 
    }

    // Cambiar estado (Cancelar / Habilitar)
    public function toggleEstadoClase()
    {
        if ($this->clase->estado === 'cancelada') {
            $this->clase->update(['estado' => 'programada']);
        } else {
            // Obtener usuarios con reservas en esta clase
            $usuariosAfectados = $this->clase->asistencias()
                ->where('estado', 'reservo')
                ->with('usuario')
                ->get()
                ->pluck('usuario');

            // Cancelar la clase
            $this->clase->update(['estado' => 'cancelada']);

            // Si hay usuarios afectados, enviar notificaciones
            if ($usuariosAfectados->isNotEmpty()) {
                // Enviar notificaciones a cada usuario
                Notification::send($usuariosAfectados, new ClaseCanceladaNotification($this->clase));

                // Disparar evento de broadcasting
                $usuariosIds = $usuariosAfectados->pluck('id')->toArray();
                event(new ClaseCancelada($this->clase, $usuariosIds));
            }
        }

        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-next-class'); // Refrescar banner de próxima clase
        $this->clase->refresh();
    }

    public function toggleUsers()
    {
        $this->showUsers = !$this->showUsers;
    }

    public function marcarAsistencia($asistenciaId, $nuevoEstado)
    {
        $asistencia = \App\Models\Asistencia::find($asistenciaId);
        
        if (!$asistencia) {
            return;
        }

        // Solo permitir cambiar entre asistio y ausente para clases pasadas
        if (in_array($nuevoEstado, ['asistio', 'ausente'])) {
            $asistencia->update(['estado' => $nuevoEstado]);
            
            // Recargar inscriptos
            $this->inscriptos = $this->clase->asistencias()->with('usuario')->where('estado', '!=', 'cancelo')->get();
        }
    }

    public function deleteClase()
    {
        // Verificaciones de seguridad
        if (!$this->clase || $this->clase->estado !== 'cancelada') {
            return;
        }

        // Eliminar
        $this->clase->delete();

        // Cerrar modal y refrescar
        $this->cerrarModal();
        $this->dispatch('refresh-calendar');
        $this->dispatch('refresh-next-class'); // Refrescar banner de próxima clase
    }

    public function render()
    {
        return view('livewire.coach.view-clase-modal');
    }
}