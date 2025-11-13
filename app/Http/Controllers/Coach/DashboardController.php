<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $coach = auth()->user();

        $nextClass = \App\Models\Clase::where('coach_id', $coach->id)
            ->where('fecha', '>=', now())
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->first();

        return view('coach.dashboard', compact('nextClass'));
    }
}
