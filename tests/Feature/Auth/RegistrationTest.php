<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\RoleSeeder;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_user_can_register_successfully(): void
    {
        $role = Role::where('nombre', 'atleta')->firstOrFail();

        $response = $this->post('/register', [
            'name' => 'Test',
            'apellido' => 'User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'dni' => '12345678',
            'telefono' => '3704123456',
            'fecha_nacimiento' => '2003-08-14',
            'rol_id' => $role->id,
        ]);

        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);

        $response->assertRedirect($user->redirectToDashboard());
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors([
            'name',
            'apellido',
            'email',
            'password',
        ]);
    }
}
