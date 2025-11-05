<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveMembership
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si no hay usuario autenticado
        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar si el usuario es un atleta
        if ($user->role->nombre !== 'atleta') {
            abort(403, 'Acceso denegado. No tienes permisos para acceder a esta sección.');
        }

        // Buscar la membresía más reciente del atleta
        $membresia = $user->membresias()->latest()->first();

        // Verificar si la membresía está activa
        if (!$membresia || $membresia->estado !== 'activa') {
            return redirect()->route('athlete.planselection')
                ->with('error', 'Debes tener membresía activa para acceder a esta sección');
        }

        // Todo correcto, continuar
        return $next($request);
    }
}
