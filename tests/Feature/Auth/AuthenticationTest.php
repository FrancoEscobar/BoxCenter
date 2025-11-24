<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/home');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_cannot_authenticate_with_invalid_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_session_can_be_destroyed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_login_redirects_based_on_user_role_through_http(): void
    {
        // Test admin
        $adminRole = \App\Models\Role::factory()->create(['nombre' => 'admin']);
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'rol_id' => $adminRole->id,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
        
        // Logout
        $this->post('/logout');

        // Test coach
        $coachRole = \App\Models\Role::factory()->create(['nombre' => 'coach']);
        $coach = User::factory()->create([
            'email' => 'coach@example.com',
            'password' => bcrypt('password123'),
            'rol_id' => $coachRole->id,
        ]);

        $response = $this->post('/login', [
            'email' => 'coach@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('coach.dashboard'));
    }

    public function test_athlete_login_flow_integrates_with_membership_system(): void
    {
        $athleteRole = \App\Models\Role::factory()->create(['nombre' => 'atleta']);
        
        // Test atleta sin membresía
        $athleteWithoutMembership = User::factory()->create([
            'email' => 'athlete1@example.com',
            'password' => bcrypt('password123'),
            'rol_id' => $athleteRole->id,
        ]);

        $response = $this->post('/login', [
            'email' => 'athlete1@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('athlete.planselection'));
        
        $this->post('/logout');

        // Test atleta con membresía activa
        $athleteWithMembership = User::factory()->create([
            'email' => 'athlete2@example.com',
            'password' => bcrypt('password123'),
            'rol_id' => $athleteRole->id,
        ]);

        $plan = \App\Models\Plan::factory()->create();
        \App\Models\Membresia::factory()->create([
            'usuario_id' => $athleteWithMembership->id,
            'plan_id' => $plan->id,
            'estado' => 'activa',
            'fecha_inicio' => now(),
            'fecha_vencimiento' => now()->addMonth(),
        ]);

        $response = $this->post('/login', [
            'email' => 'athlete2@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('athlete.dashboard'));
    }
}