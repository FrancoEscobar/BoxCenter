<?php
namespace App\Services;

use App\Models\Membresia;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class MembershipService
{
    public function createPendingMembership(int $planId, int $tipoEntrenamientoId): Membresia
    {
        $user = Auth::user();
        $plan = Plan::findOrFail($planId);

        return Membresia::firstOrCreate(
            [
                'usuario_id' => $user->id,
                'plan_id' => $planId,
                'tipo_entrenamiento_id' => $tipoEntrenamientoId,
                'estado' => 'pago_pendiente',
            ],
            [
                'precio' => $plan->precio ?? 0,
            ]
        );
    }
}
