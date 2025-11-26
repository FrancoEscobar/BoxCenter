<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Buscar la próxima clase reservada del usuario
        $proximaClase = Asistencia::where('usuario_id', Auth::id())
            ->whereIn('estado', ['ausente']) // Solo clases reservadas (no canceladas ni ya asistidas)
            ->whereHas('clase', function ($query) {
                $query->where('estado', 'programada')
                      ->where('fecha', '>=', Carbon::today());
            })
            ->with(['clase.tipo_entrenamiento', 'clase.coach'])
            ->orderBy('created_at')
            ->first();

        return view('athlete.dashboard', [
            'proximaClase' => $proximaClase
        ]);
    }
}
