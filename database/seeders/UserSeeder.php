<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

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
            'rol_id'   => $adminRoleId,
            'name'     => 'Administrador',
            'apellido' => 'BoxCenter',
            'email'    => 'admin@boxcenter.com',
            'dni'      => '12345678',
            'telefono'  => '3704123456',
            'fecha_nacimiento' => '2003-08-14',
            'password' => Hash::make('password')
        ]);

        // Coach
        User::create([
            'rol_id'   => $coachRoleId,
            'name'     => 'Coach',
            'apellido' => 'BoxCenter',
            'email'    => 'coach@boxcenter.com',
            'dni'      => '87654321',
            'telefono'  => '3704123456',
            'fecha_nacimiento' => '2000-05-20',
            'password' => Hash::make('password')
        ]);

        // Atleta
        User::create([
            'rol_id'   => $atletaRoleId,
            'name'     => 'Atleta',
            'apellido' => 'BoxCenter',
            'email'    => 'atleta@boxcenter.com',
            'dni'      => '56781234',
            'telefono'  => '3704123456',
            'fecha_nacimiento' => '2003-08-14',
            'password' => Hash::make('password')
        ]);
    }
}
