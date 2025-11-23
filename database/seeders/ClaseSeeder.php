<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clase;
use App\Models\User;
use App\Models\TipoEntrenamiento;
use App\Models\Role;
use Carbon\Carbon;

class ClaseSeeder extends Seeder
{
    public function run(): void
    {
        $rolCoach = Role::where('nombre', 'coach')->first();

        $coach = User::where('rol_id', $rolCoach->id)->first();

        $tipo = TipoEntrenamiento::firstOrCreate(['nombre' => 'CrossFit']);

        $fechas = [
            Carbon::now()->subDay(),
            Carbon::now()->addDay(),
            Carbon::now()->addDays(2),
            Carbon::now()->addDays(3),
        ];

        foreach ($fechas as $index => $fecha) {
            Clase::create([
                'fecha' => $fecha->format('Y-m-d'),
                'hora_inicio' => '22:00:00',
                'hora_fin' => '23:00:00',
                'tipo_entrenamiento_id' => $tipo->id,
                'coach_id' => $coach->id,
                'estado' => $fecha->isPast() ? 'realizada' : 'programada',
                'cupo' => 12,
            ]);
        }
    }
}
