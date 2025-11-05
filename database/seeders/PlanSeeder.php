<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'nombre' => 'Mensual',
            'descripcion' => 'Acceso completo durante un mes.',
            'duracion' => 30,
            'precio' => 35000,
        ]);

        Plan::create([
            'nombre' => 'Semanal',
            'descripcion' => 'Acceso completo durante una semana.',
            'duracion' => 7,
            'precio' => 10000,
        ]);
    }
}
