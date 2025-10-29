<?php

namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'admin', 'descripcion' => 'Administrador del sistema'],
            ['nombre' => 'coach', 'descripcion' => 'Entrenador'],
            ['nombre' => 'atleta', 'descripcion' => 'Atleta'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['nombre' => $role['nombre']],
                [
                    'descripcion' => $role['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
