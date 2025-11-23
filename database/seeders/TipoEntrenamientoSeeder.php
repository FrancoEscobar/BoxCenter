<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoEntrenamiento;

class TipoEntrenamientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoEntrenamiento::create([
            'nombre' => 'CrossFit',
            'descripcion' => 'Entrenamiento de alta intensidad que combina levantamiento de pesas, gimnasia y cardio.',
        ]);

        tipoentrenamiento::create([
            'nombre' => 'Funcional',
            'descripcion' => 'Entrenamiento que mejora la fuerza, el equilibrio y la coordinación para actividades diarias.',
        ]);
    }
}
