<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clase;
use App\Models\User;
use App\Models\Asistencia;
use App\Models\TipoEntrenamiento;
use App\Models\Role;
use Carbon\Carbon;

class ClaseSeeder extends Seeder
{
    public function run(): void
    {
        $rolCoach = Role::where('nombre', 'coach')->first();
        $rolAtleta = Role::where('nombre', 'atleta')->first();

        $coach = User::where('rol_id', $rolCoach->id)->first();
        $atleta = User::where('rol_id', $rolAtleta->id)->first();

        $tipo = TipoEntrenamiento::firstOrCreate(['nombre' => 'CrossFit']);

        // Solo 2 clases: ayer y anteayer, ambas realizadas
        $fechas = [
            Carbon::today()->subDay(),      // ayer
            Carbon::today()->subDays(2),    // anteayer
        ];

        $clasesCreadas = [];

        foreach ($fechas as $fecha) {
            $clase = Clase::create([
                'fecha' => $fecha->format('Y-m-d'),
                'hora_inicio' => '22:00:00',
                'hora_fin' => '23:00:00',
                'tipo_entrenamiento_id' => $tipo->id,
                'coach_id' => $coach->id,
                'estado' => 'realizada',
                'cupo' => 12,
            ]);

            $clasesCreadas[] = $clase;
        }

        // Inscripciones de ejemplo: una asistida y otra no asistida
        if ($atleta && count($clasesCreadas) === 2) {
            Asistencia::create([
                'clase_id' => $clasesCreadas[0]->id,
                'usuario_id' => $atleta->id,
                'estado' => 'asistio',
            ]);

            Asistencia::create([
                'clase_id' => $clasesCreadas[1]->id,
                'usuario_id' => $atleta->id,
                'estado' => 'ausente',
            ]);
        }
    }
}
