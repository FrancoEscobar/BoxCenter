<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TipoEntrenamiento;
use App\Models\Plan;
use App\Models\Membresia;
use Illuminate\Support\Facades\Auth;

// Este componente se encarga de:
// - Cargar los tipos de entrenamiento (CrossFit, Funcional, etc.) y los planes (mensual, semanal, etc.).
// - Detectar qué selecciona el usuario.
// - Mostrar un resumen de la selección antes del pago.
// - Comunicar cambios a la vista en tiempo real.

class PlanSelection extends Component
{
    
    public $tipos_entrenamiento = [];
    public $planes = [];
    public $entrenamientoSeleccionado = null;
    public $planSeleccionado = null;
    public $mostrarResumen = false;

    // Se ejecuta al inicializar el componente. Carga todos los tipos de entrenamiento y planes desde la base de datos
    public function mount()
    {
        $this->tipos_entrenamiento = TipoEntrenamiento::all();
        $this->planes = Plan::all();
    }

    // Se ejecuta cuando el usuario hace clic en un tipo de entrenamiento
    public function seleccionarEntrenamiento($id)
    {
        $this->entrenamientoSeleccionado = TipoEntrenamiento::find($id);
    }

    // Igual que el anterior, pero para el plan
    public function seleccionarPlan($id)
    {
        $this->planSeleccionado = Plan::find($id);
    }

    // Valida que se haya elegido un tipo de entrenamiento y un plan
    private function validarSeleccion(): bool
    {
        if (!$this->entrenamientoSeleccionado || !$this->planSeleccionado) {
            session()->flash('error', 'Debes seleccionar un tipo de entrenamiento y un plan.');
            return false;
        }
        return true;
    }

    // Muestra el resumen si la selección es válida
    public function verResumen()
    {
        if ($this->validarSeleccion()) {
            $this->mostrarResumen = true;
        }
    }

    // Redirige al usuario al módulo de pago si cumple con los requisitos
    public function continuarAlPago()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }


        // Validar selección
        if (!$this->validarSeleccion()) {
            return;
        }

        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Verificar si el usuario ya tiene una membresía activa
        $membresiaActiva = Membresia::where('usuario_id', $usuario->id)
            ->where('estado', ['activa'])
            ->first();

        // Si tiene una membresía activa, no permitir crear ni modificar
        if ($membresiaActiva) {
            session()->flash('error', 'Ya tienes una membresía activa. No puedes iniciar otra hasta que expire o la canceles.');
            return;
        }

        if (!$this->entrenamientoSeleccionado || !$this->planSeleccionado) {
            session()->flash('error', 'Selección inválida.');
            return;
        }

        // Guardar la selección en la sesión para usarla en el módulo de pago
        session([
            'tipo_entrenamiento_id' => $this->entrenamientoSeleccionado->id,
            'plan_id' => $this->planSeleccionado->id,
            'importe' => $this->planSeleccionado->precio
        ]);

        // Redirigir al módulo de pago
        return redirect()->route('athlete.payment');
    }

    // Devuelve la vista del componente
    public function render()
    {
        return view('livewire.plan-selection');
    }
}
