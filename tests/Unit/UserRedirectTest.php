<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Models\Membresia;
use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_redirect_returns_admin_dashboard_route(): void
    {
        $adminRole = Role::factory()->create(['nombre' => 'admin']);
        $admin = User::factory()->create(['rol_id' => $adminRole->id]);

        $this->assertEquals(
            route('admin.dashboard'),
            $admin->redirectToDashboard()
        );
    }

    public function test_coach_redirect_returns_coach_dashboard_route(): void
    {
        $coachRole = Role::factory()->create(['nombre' => 'coach']);
        $coach = User::factory()->create(['rol_id' => $coachRole->id]);

        $this->assertEquals(
            route('coach.dashboard'),
            $coach->redirectToDashboard()
        );
    }

    public function test_athlete_without_membership_redirects_to_plan_selection(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);

        $this->assertEquals(
            route('athlete.planselection'),
            $athlete->redirectToDashboard()
        );
    }

    public function test_athlete_with_active_membership_redirects_to_athlete_dashboard(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);
        
        $plan = \App\Models\Plan::factory()->create();
        Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => now(),
            'fecha_vencimiento' => now()->addMonth(),
        ]);

        $this->assertEquals(
            route('athlete.dashboard'),
            $athlete->redirectToDashboard()
        );
    }

    public function test_athlete_with_expired_membership_by_status_redirects_to_plan_selection(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);
        
        $plan = \App\Models\Plan::factory()->create();
        Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'vencida',
            'fecha_inicio' => now()->subMonth(),
            'fecha_vencimiento' => now()->subDay(),
        ]);

        $this->assertEquals(
            route('athlete.planselection'),
            $athlete->redirectToDashboard()
        );
    }

    public function test_athlete_with_expired_membership_by_date_redirects_to_plan_selection(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);
        
        $plan = \App\Models\Plan::factory()->create();
        Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'activa', // Estado activo pero fecha vencida
            'fecha_inicio' => now()->subMonth(),
            'fecha_vencimiento' => now()->subDay(),
        ]);

        $this->assertEquals(
            route('athlete.planselection'),
            $athlete->redirectToDashboard()
        );
    }

    public function test_athlete_with_pending_payment_redirects_to_payment_pending(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);
        
        $plan = \App\Models\Plan::factory()->create();
        $membresia = Membresia::factory()->create([
            'usuario_id' => $athlete->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => now(),
            'fecha_vencimiento' => now()->addMonth(),
        ]);

        // Crear método de pago directamente en la tabla
        \Illuminate\Support\Facades\DB::table('metodos_pago')->insert([
            'nombre' => 'Tarjeta',
            'descripcion' => 'Pago con tarjeta',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Pago::factory()->create([
            'membresia_id' => $membresia->id,
            'metodo_pago_id' => 1,
            'status' => 'pending',
            'payment_id' => 'test-payment-123',
        ]);

        $redirectUrl = $athlete->redirectToDashboard();
        
        $this->assertStringContainsString(
            route('athlete.payment.pending', ['payment_id' => 'test-payment-123']),
            $redirectUrl
        );
    }

    public function test_user_without_recognized_role_redirects_to_home(): void
    {
        $unknownRole = Role::factory()->create(['nombre' => 'desconocido']);
        $user = User::factory()->create(['rol_id' => $unknownRole->id]);

        $this->assertEquals(
            route('home'),
            $user->redirectToDashboard()
        );
    }

    public function test_user_without_role_redirects_to_home(): void
    {
        $user = User::factory()->create(['rol_id' => null]);

        $this->assertEquals(
            route('home'),
            $user->redirectToDashboard()
        );
    }
}
