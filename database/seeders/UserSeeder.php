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

        // Atleta con membresía activa
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

        // Atleta con membresía vencida
        User::create([
            'rol_id'   => $atletaRoleId,
            'name'     => 'María',
            'apellido' => 'García',
            'email'    => 'maria.garcia@boxcenter.com',
            'dni'      => '45123678',
            'telefono'  => '3704567890',
            'fecha_nacimiento' => '1995-03-15',
            'password' => Hash::make('password')
        ]);

        // Atleta sin membresía
        User::create([
            'rol_id'   => $atletaRoleId,
            'name'     => 'Juan',
            'apellido' => 'Pérez',
            'email'    => 'juan.perez@boxcenter.com',
            'dni'      => '39876543',
            'telefono'  => '3704123789',
            'fecha_nacimiento' => '1998-11-22',
            'password' => Hash::make('password')
        ]);
    }
}
