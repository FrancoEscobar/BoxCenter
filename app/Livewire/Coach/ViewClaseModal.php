<?php

namespace App\Livewire\Coach;

use Livewire\Component;
use App\Models\Clase;
use App\Models\User;
use App\Models\Role;
use Livewire\Attributes\On;

class ViewClaseModal extends Component
{
    public $clase;
    public $wod;
    public $inscriptos;
    public $showUsers = false; // Toggle para mostrar/ocultar lista de usuarios

    public $isEditing = false; // Indicador de modo edición
    public $coaches; // Lista de coaches para el select

    // Campos temporales para editar
    public $edit_fecha;
    public $edit_hora_inicio;
    public $edit_hora_fin;
    public $edit_coach_id;
    public $edit_cupo;

    #[On('open-view-modal')]
    public function openModal($claseId)
    {
        // Cargamos la clase con todas las relaciones necesarias
        $this->clase = Clase::with([
            'tipo_entrenamiento', 
            'coach', 
            'wod.ejercicios',
            'asistencias.usuario' // Para obtener la lista de inscriptos
        ])->find($claseId);

        if (!$this->clase) return;

        $this->wod = $this->clase->wod;
        
        $this->inscriptos = $this->clase->asistencias->where('estado', '!=', 'cancelo');
        $this->showUsers = false; // Asegurar que la lista de usuarios esté oculta al abrir
        $this->isEditing = false; // Asegurar que no esté en modo edición al abrir
        
        // Cargar lista de coaches para el select
        $coachRole = Role::where('nombre', 'coach')->first();
        $this->coaches = User::where('rol_id', $coachRole->id)->orderBy('name')->get();

        $this->dispatch('show-view-modal'); // Evento JS para abrir el modal
    }

    // Activar modo edición y llenar los inputs con los datos actuales
    public function startEditing()
    {
        $this->edit_fecha       = $this->clase->fecha->format('Y-m-d');
        $this->edit_hora_inicio = $this->clase->hora_inicio->format('H:i');
        $this->edit_hora_fin    = $this->clase->hora_fin->format('H:i');
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
        $this->validate([
            'edit_fecha'       => 'required|date',
            'edit_hora_inicio' => 'required',
            'edit_hora_fin'    => 'required|after:edit_hora_inicio',
            'edit_coach_id'    => 'required|exists:users,id',
            'edit_cupo'        => 'required|integer|min:1',
        ]);

        $this->clase->update([
            'fecha'       => $this->edit_fecha,
            'hora_inicio' => $this->edit_hora_inicio,
            'hora_fin'    => $this->edit_hora_fin,
            'coach_id'    => $this->edit_coach_id,
            'cupo'        => $this->edit_cupo,
        ]);

        $this->isEditing = false;
        $this->dispatch('refresh-calendar'); // Refrescar calendario
        
        // Recargar modelo para ver cambios en el modal sin cerrar
        $this->clase->refresh(); 
    }

    // Cambiar estado (Cancelar / Habilitar)
    public function toggleEstadoClase()
    {
        if ($this->clase->estado === 'cancelada') {
            $this->clase->update(['estado' => 'programada']);
        } else {
            $this->clase->update(['estado' => 'cancelada']);
        }

        $this->dispatch('refresh-calendar');
        $this->clase->refresh();
    }

    public function toggleUsers()
    {
        $this->showUsers = !$this->showUsers;
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
        $this->dispatch('hide-view-modal');
        $this->dispatch('refresh-calendar');
    }

    public function render()
    {
        return view('livewire.coach.view-clase-modal');
    }
}