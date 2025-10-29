<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role; // Asegurate de tener el modelo Role

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener los IDs de los roles
        $adminRoleId   = Role::where('nombre', 'admin')->first()->id;
        $coachRoleId   = Role::where('nombre', 'coach')->first()->id;
        $atletaRoleId = Role::where('nombre', 'atleta')->first()->id;

        // Admin
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@boxcenter.com',
            'password' => Hash::make('admin123'),
            'rol_id'   => $adminRoleId,
        ]);

        // Coach
        User::create([
            'name'     => 'Coach Franco',
            'email'    => 'coach@boxcenter.com',
            'password' => Hash::make('coach123'),
            'rol_id'   => $coachRoleId,
        ]);

        // Athlete
        User::create([
            'name'     => 'Atleta Juan',
            'email'    => 'atleta@boxcenter.com',
            'password' => Hash::make('atleta123'),
            'rol_id'   => $atletaRoleId,
        ]);
    }
}
