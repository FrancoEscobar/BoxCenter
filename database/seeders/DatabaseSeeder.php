<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TipoEntrenamientoSeeder::class,
            PlanSeeder::class,
            MetodosPagoSeeder::class,
            MembresiaSeeder::class,
            ClaseSeeder::class,
            EjercicioSeeder::class,
            WodSeeder::class,
        ]);
    }
}