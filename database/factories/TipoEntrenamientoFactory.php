<?php

namespace Database\Factories;

use App\Models\TipoEntrenamiento;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoEntrenamientoFactory extends Factory
{
    protected $model = TipoEntrenamiento::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
        ];
    }
}
