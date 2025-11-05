<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Membresia;
use App\Models\Plan;
use App\Models\TipoEntrenamiento;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {   
        // Obtener datos de sesión
        $planId = Session::get('plan_id');
        $tipoEntrenamientoId = Session::get('tipo_entrenamiento_id');

        // Verificar que el plan y tipo de entrenamiento estén en sesión
        if (!$planId || !$tipoEntrenamientoId) {
            return redirect()->route('athlete.planselection')
                ->with('error', 'Debés seleccionar un plan antes de continuar al pago.');
        }

        // Obtener detalles del plan y tipo de entrenamiento
        $plan = Plan::find($planId);
        $tipoEntrenamiento = TipoEntrenamiento::find($tipoEntrenamientoId);

        // Mostrar vista de pago
        return view('athlete.payment', compact('plan', 'tipoEntrenamiento'));
    }

    // Procesar el pago (simulado para pruebas)
    public function procesarPago(Request $request)
    {   
        // Obtener datos de sesión
        $planId = Session::get('plan_id');
        $tipoEntrenamientoId = Session::get('tipo_entrenamiento_id');
        $user = Auth::user();

        // Verificar que el plan y tipo de entrenamiento estén en sesión
        if (!$planId || !$tipoEntrenamientoId) {
            return redirect()->route('athlete.planselection')
                ->with('error', 'Debés seleccionar un plan antes de pagar.');
        }

        // Verificar si ya existe una membresía pendiente
        $membresiaExistente = Membresia::where('usuario_id', $user->id)
            ->where('estado', 'pago_pendiente')
            ->first();

        if ($membresiaExistente) {
            // Si ya existe, redirigir a la vista de pago actual
            Session::put('membresia_id', $membresiaExistente->id);
            return redirect()->route('athlete.payment')
                ->with('error', 'Ya tenés una membresía pendiente de pago.');
        }

        // Obtener detalles del plan
        $plan = Plan::find($planId);

        // Crear membresía (prueba simple)
        $membresia = Membresia::create([
            'usuario_id' => $user->id,
            'plan_id' => $planId,
            'tipo_entrenamiento_id' => $tipoEntrenamientoId,
            'estado' => 'pago_pendiente',
            'importe' => $plan->precio ?? 0,
        ]);

        // Guardar ID en sesión para verificar
        Session::put('membresia_id', $membresia->id);

        // Mostrar confirmación, solo para pruebas
        return back()->with('success', 'Membresía creada correctamente (ID: ' . $membresia->id . ')');
    }
}
