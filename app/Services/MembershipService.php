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

        $membresia = Membresia::create([
            'usuario_id' => $user->id,
            'plan_id' => $planId,
            'tipo_entrenamiento_id' => $tipoEntrenamientoId,
            'estado' => 'pago_pendiente',
            'importe' => $plan->precio ?? 0,
        ]);

        return $membresia;
    }

    public function activateMembership($membershipId)
    {
        \Log::info('Activando membresía', ['id' => $membershipId]);

        $membership = Membresia::find($membershipId);

        if (!$membership) {
            \Log::warning('Membresía no encontrada', ['id' => $membershipId]);
            return;
        }

        $plan = $membership->plan;

        $fechaInicio = now();
        $fechaVencimiento = $fechaInicio->copy()->addDays($plan->duracion);

        $membership->update([
            'estado' => 'activa',
            'fecha_inicio' => $fechaInicio->toDateString(),
            'fecha_vencimiento' => $fechaVencimiento->toDateString(),
        ]);

        \Log::info('Membresía activada', ['membership' => $membership->toArray()]);
    }
}
