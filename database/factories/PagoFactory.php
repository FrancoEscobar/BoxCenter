<?php

namespace Database\Factories;

use App\Models\Pago;
use App\Models\Membresia;
use App\Models\MetodoPago;
use Illuminate\Database\Eloquent\Factories\Factory;

class PagoFactory extends Factory
{
    protected $model = Pago::class;

    public function definition()
    {
        return [
            'membresia_id' => Membresia::factory(),
            'payment_id' => (string) $this->faker->unique()->randomNumber(9),
            'fecha' => now(),
            'metodo_pago_id' => 1,
            'importe' => 1500,
            'status' => 'pending',
        ];
    }
}