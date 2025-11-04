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
        return view('livewire.plan-selection');
    }

    public function continuarAlPago()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->entrenamientoSeleccionado || !$this->planSeleccionado) {
            session()->flash('error', 'Debes seleccionar un tipo de entrenamiento y un plan.');
            return;
        }

        $usuario = Auth::user();

        // Buscar si el usuario ya tiene una membresía activa o pendiente
        $membresiaExistente = Membresia::where('usuario_id', $usuario->id)
            ->whereIn('estado', ['activa', 'pendiente'])
            ->first();

        // Si tiene una membresía activa, no permitir crear ni modificar
        if ($membresiaExistente && $membresiaExistente->estado === 'activa') {
            session()->flash('error', 'Ya tienes una membresía activa. No puedes crear otra.');
            return;
        }

        if (!$this->entrenamientoSeleccionado || !$this->planSeleccionado) {
            session()->flash('error', 'Selección inválida.');
            return;
        }

        // Si tiene una pendiente se actualiza con la nueva selección
        if ($membresiaExistente && $membresiaExistente->estado === 'pendiente') {
            $membresiaExistente->update([
                'tipo_entrenamiento_id' => $this->entrenamientoSeleccionado->id,
                'plan_id' => $this->planSeleccionado->id,
                'importe' => $this->planSeleccionado->precio,
                'updated_at' => now(),
            ]);

            session()->flash('success', 'Tu membresía pendiente fue actualizada correctamente.');
            return redirect()->route('athlete.payment');
        }

        // Si no existe ninguna membresía activa o pendiente se crea una nueva
        $membresia = Membresia::create([
            'usuario_id' => $usuario->id,
            'tipo_entrenamiento_id' => $this->entrenamientoSeleccionado->id,
            'plan_id' => $this->planSeleccionado->id,
            'estado' => 'pendiente',
            'importe' => $this->planSeleccionado->precio,
        ]);

        // Guardar el ID en sesión para usarlo en el módulo de pago
        session(['membresia_id' => $membresia->id]);

        // Redirigir al módulo de pago
        return redirect()->route('athlete.payment');
    }
}
