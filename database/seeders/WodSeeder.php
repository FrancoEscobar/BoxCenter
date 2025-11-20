<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wod;
use App\Models\Ejercicio;
use Carbon\Carbon;

class WodSeeder extends Seeder
{
    public function run(): void
    {
        // Ejercicios existentes
        $sentadilla = Ejercicio::where('nombre', 'Sentadilla')->first();
        $burpees = Ejercicio::where('nombre', 'Burpees')->first();
        $dominadas = Ejercicio::where('nombre', 'Dominadas')->first();

        $wod1 = Wod::create([
            'nombre' => 'Full Body Starter',
            'descripcion' => 'Entrenamiento básico para comenzar.',
            'user_id' => 2,
            'tipo_entrenamiento_id' => 1,
            'duracion' => 20,
            'fecha_creacion' => Carbon::now(),
        ]);

        $wod1->ejercicios()->attach([
            $sentadilla->id => [
                'orden' => 1,
                'series' => 3,
                'repeticiones' => 15,
                'duracion' => 0,
            ],
            $burpees->id => [
                'orden' => 2,
                'series' => 3,
                'repeticiones' => 10,
                'duracion' => 0,
            ],
        ]);

        $wod2 = Wod::create([
            'nombre' => 'Upper Body Power',
            'descripcion' => 'Trabajo enfocado en torso.',
            'user_id' => 2,
            'tipo_entrenamiento_id' => 1,
            'duracion' => 18,
            'fecha_creacion' => Carbon::now(),
        ]);

        $wod2->ejercicios()->attach([
            $burpees->id => [
                'orden' => 1,
                'series' => 4,
                'repeticiones' => 12,
                'duracion' => 0,
            ],
            $dominadas->id => [
                'orden' => 2,
                'series' => 4,
                'repeticiones' => 8,
                'duracion' => 0,
            ],
        ]);
    }
}