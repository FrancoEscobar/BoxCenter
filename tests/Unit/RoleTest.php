<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_can_be_created_with_nombre_and_descripcion(): void
    {
        $role = Role::factory()->create([
            'nombre' => 'admin',
            'descripcion' => 'Administrator role',
        ]);

        $this->assertEquals('admin', $role->nombre);
        $this->assertEquals('Administrator role', $role->descripcion);
    }

    public function test_user_belongs_to_a_role(): void
    {
        $role = Role::factory()->create(['nombre' => 'atleta']);
        $user = User::factory()->create(['rol_id' => $role->id]);

        $this->assertInstanceOf(Role::class, $user->role);
        $this->assertEquals('atleta', $user->role->nombre);
    }

    public function test_admin_role_can_be_identified(): void
    {
        $adminRole = Role::factory()->create(['nombre' => 'admin']);
        $admin = User::factory()->create(['rol_id' => $adminRole->id]);

        $this->assertEquals('admin', $admin->role->nombre);
    }

    public function test_coach_role_can_be_identified(): void
    {
        $coachRole = Role::factory()->create(['nombre' => 'coach']);
        $coach = User::factory()->create(['rol_id' => $coachRole->id]);

        $this->assertEquals('coach', $coach->role->nombre);
    }

    public function test_atleta_role_can_be_identified(): void
    {
        $athleteRole = Role::factory()->create(['nombre' => 'atleta']);
        $athlete = User::factory()->create(['rol_id' => $athleteRole->id]);

        $this->assertEquals('atleta', $athlete->role->nombre);
    }

    public function test_user_can_exist_without_a_role(): void
    {
        $user = User::factory()->create(['rol_id' => null]);

        $this->assertNull($user->role);
    }
}
