<?php

namespace Database\Factories;

use App\Models\Membresia;
use App\Models\User;
use App\Models\TipoEntrenamiento;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembresiaFactory extends Factory
{
    protected $model = Membresia::class;

    public function definition()
    {
        return [
            'usuario_id' => User::factory(),
            'tipo_entrenamiento_id' => TipoEntrenamiento::factory(),
            'plan_id' => Plan::factory(),
            'estado' => 'pago_pendiente',
            'importe' => 1500,
        ];
    }
}
