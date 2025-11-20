<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ejercicio;

class EjercicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ejercicios = [
            [
                'nombre' => 'Sentadilla',
                'descripcion' => 'Ejercicio básico para piernas.'
            ],
            [
                'nombre' => 'Press de Banca',
                'descripcion' => 'Ejercicio de pecho con barra.'
            ],
            [
                'nombre' => 'Dominadas',
                'descripcion' => 'Tracción en barra fija.'
            ],
            [
                'nombre' => 'Burpees',
                'descripcion' => 'Ejercicio cardiovascular de cuerpo completo.'
            ],
            [
                'nombre' => 'Remo con Mancuerna',
                'descripcion' => 'Trabajo de espalda unilateral.'
            ],
        ];

        foreach ($ejercicios as $item) {
            Ejercicio::create($item);
        }
    }
}
