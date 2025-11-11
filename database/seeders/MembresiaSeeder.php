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
        $usuario = User::find(3);
        $tipoEntrenamiento = TipoEntrenamiento::first();
        $plan = Plan::first();

        Membresia::create([
            'usuario_id' => $usuario->id,
            'tipo_entrenamiento_id' => $tipoEntrenamiento->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => Carbon::now(),
            'fecha_vencimiento' => Carbon::now()->addMonth(),
            'descuento' => 0,
            'importe' => $plan->precio,
        ]);
    }
}
