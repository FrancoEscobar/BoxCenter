<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'rol_id' => Role::factory(), // por defecto crea un rol si no se pasa
            'name' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'dni' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'telefono' => $this->faker->phoneNumber(),
            'fecha_nacimiento' => $this->faker->date(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password por defecto
            'remember_token' => Str::random(10),
        ];
    }

    // Método para asignar un rol específico
    public function withRole(string $roleName): static
    {
        return $this->state(function () use ($roleName) {
            $role = Role::firstOrCreate(['nombre' => $roleName]);
            return ['rol_id' => $role->id];
        });
    }
}
