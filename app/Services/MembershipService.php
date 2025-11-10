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

        $membership->estado = 'activa';
        $membership->fecha_inicio = now()->toDateString();
        $membership->fecha_vencimiento = now()->addMonth()->toDateString();
        $membership->save();

        \Log::info('Membresía activada', ['membership' => $membership->toArray()]);
    }
}
