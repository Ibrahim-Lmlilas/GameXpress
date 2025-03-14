<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les rôles nécessaires
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'user_manager']);

        // Créer un utilisateur admin pour les tests
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super_admin');
    }

    public function it_can_list_users()
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/v1/admin/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         '*' => ['id', 'name', 'email', 'created_at', 'updated_at', 'roles']
                     ]
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_user()
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user_manager'
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/v1/admin/users', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => ['id', 'name', 'email', 'created_at', 'updated_at', 'roles']
                 ]);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);
    }

    public function it_can_update_a_user()
    {
        $user = User::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'role' => 'user_manager'
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->putJson("/api/v1/admin/users/{$user->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => ['id', 'name', 'email', 'created_at', 'updated_at', 'roles']
                 ]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => $data['email']]);
    }

    public function it_can_delete_a_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->deleteJson("/api/v1/admin/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message'
                 ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
