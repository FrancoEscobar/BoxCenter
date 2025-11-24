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
            'rol_id' => null, // será asignado por withRole o manualmente
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
    public function withRole($role): static
    {
        return $this->state(function () use ($role) {
            if (is_string($role)) {
                $roleModel = Role::factory()->create(['nombre' => $role]);
                return ['rol_id' => $roleModel->id];
            }
            return ['rol_id' => is_object($role) ? $role->id : $role];
        });
    }
}
