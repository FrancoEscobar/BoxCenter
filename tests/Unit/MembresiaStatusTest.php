<?php

namespace Tests\Unit;

use App\Models\Membresia;
use App\Models\Plan;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembresiaStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_membresia_is_active_when_estado_is_activa_and_not_expired(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);
        $plan = Plan::factory()->create();

        $membresia = Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => now(),
            'fecha_vencimiento' => now()->addMonth(),
        ]);

        $this->assertEquals('activa', $membresia->estado);
        $this->assertTrue($membresia->fecha_vencimiento > now());
    }

    public function test_membresia_is_expired_when_estado_is_vencida(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);
        $plan = Plan::factory()->create();

        $membresia = Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'vencida',
            'fecha_inicio' => now()->subMonth(),
            'fecha_vencimiento' => now()->subDay(),
        ]);

        $this->assertEquals('vencida', $membresia->estado);
    }

    public function test_membresia_is_expired_when_fecha_vencimiento_is_in_the_past(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);
        $plan = Plan::factory()->create();

        $membresia = Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => now()->subMonth(),
            'fecha_vencimiento' => now()->subDay(),
        ]);

        $this->assertTrue($membresia->fecha_vencimiento < now());
    }

    public function test_user_has_no_membresias_returns_null(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);

        $latestMembresia = $athlete->membresias()->latest()->first();

        $this->assertNull($latestMembresia);
    }

    public function test_user_latest_membresia_returns_most_recent(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);
        $plan = Plan::factory()->create();

        // Crear membresía antigua
        $oldMembresia = Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'vencida',
            'fecha_inicio' => now()->subMonths(2),
            'fecha_vencimiento' => now()->subMonth(),
            'created_at' => now()->subMonths(2),
        ]);

        // Crear membresía reciente
        $newMembresia = Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => now(),
            'fecha_vencimiento' => now()->addMonth(),
            'created_at' => now(),
        ]);

        $latestMembresia = $athlete->membresias()->latest()->first();

        $this->assertEquals($newMembresia->id, $latestMembresia->id);
        $this->assertNotEquals($oldMembresia->id, $latestMembresia->id);
    }
}
