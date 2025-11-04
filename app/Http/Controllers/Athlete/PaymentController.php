<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Models\Membresia;

class PaymentController extends Controller
{
    public function index()
    {
        // Obtener la membresía creada
        $membresiaId = Session::get('membresia_id');

        if (!$membresiaId) {
            return redirect()->route('athlete.memberships')
                             ->with('error', 'No se encontró una membresía pendiente.');
        }

        $membresia = Membresia::with(['plan', 'tipoEntrenamiento'])->findOrFail($membresiaId);

        return view('athlete.payment', compact('membresia'));
    }
}
