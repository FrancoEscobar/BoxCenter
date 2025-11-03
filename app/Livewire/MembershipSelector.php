<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TipoEntrenamiento;
use App\Models\Plan;

// Este componente se encarga de:
// - Cargar los tipos de entrenamiento (CrossFit, Funcional, etc.) y los planes (mensual, semanal, etc.).
// - Detectar qué selecciona el usuario.
// - Mostrar un resumen de la selección antes del pago.
// - Comunicar cambios a la vista en tiempo real.

class MembershipSelector extends Component
{
    
    public $tipos_entrenamiento = [];
    public $planes = [];
    public $entrenamientoSeleccionado = null;
    public $planSeleccionado = null;
    public $mostrarResumen = false;

    // Se ejecuta al inicializar el componente. Carga todos los tipos de entrenamiento y planes desde la base de datos.
    public function mount()
    {
        $this->tipos_entrenamiento = TipoEntrenamiento::all();
        $this->planes = Plan::all();
    }

    // Se ejecuta cuando el usuario hace clic en un tipo de entrenamiento.
    public function seleccionarEntrenamiento($id)
    {
        $this->entrenamientoSeleccionado = TipoEntrenamiento::find($id);
    }

    // Igual que el anterior, pero para el plan.
    public function seleccionarPlan($id)
    {
        $this->planSeleccionado = Plan::find($id);
    }

    // Valida que se haya elegido un tipo de entrenamiento y un plan. Muestra el resumen.
    public function verResumen()
    {   
        if ($this->entrenamientoSeleccionado && $this->planSeleccionado) {
            $this->mostrarResumen = true;
        } else {
            session()->flash('error', 'Debes seleccionar una tipo de entrenamiento y un plan.');
        }
    }

    // Devuelve la vista del componente.
    public function render()
    {
        return view('livewire.membership-selector');
    }
}
