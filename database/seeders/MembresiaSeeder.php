<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membresia;
use App\Models\User;
use App\Models\TipoEntrenamiento;
use App\Models\Plan;
use Carbon\Carbon;

class MembresiaSeeder extends Seeder
{
    public function run(): void
    {
        // Membresía activa para primer atleta (ID 3)
        $usuario1 = User::find(3);
        $tipoEntrenamiento = TipoEntrenamiento::first();
        $plan = Plan::first();

        Membresia::create([
            'usuario_id' => $usuario1->id,
            'tipo_entrenamiento_id' => $tipoEntrenamiento->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => Carbon::now(),
            'fecha_vencimiento' => Carbon::now()->addMonth(),
            'descuento' => 0,
            'importe' => $plan->precio,
        ]);

        // Membresía vencida para segundo atleta (ID 4)
        $usuario2 = User::find(4);
        Membresia::create([
            'usuario_id' => $usuario2->id,
            'tipo_entrenamiento_id' => $tipoEntrenamiento->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => Carbon::now()->subMonths(2),
            'fecha_vencimiento' => Carbon::now()->subDays(15), // Vencida hace 15 días
            'descuento' => 0,
            'importe' => $plan->precio,
        ]);

        // Atleta sin membresía (ID 5) - no se crea ningún registro
    }
}
