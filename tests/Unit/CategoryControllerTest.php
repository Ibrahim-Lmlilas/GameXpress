<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les rôles nécessaires
        Role::firstOrCreate(['name' => 'super_admin']);

        // Créer un utilisateur admin pour les tests
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super_admin');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_list_categories()
    {
        Category::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/v1/admin/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         '*' => ['id', 'name', 'slug', 'parent_id', 'created_at', 'updated_at']
                     ]
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_create_a_category()
    {
        $data = [
            'name' => $this->faker->word,
            'parent_id' => null
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/v1/admin/categories', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => ['id', 'name', 'slug', 'parent_id', 'created_at', 'updated_at']
                 ]);

        $this->assertDatabaseHas('categories', ['name' => $data['name']]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_a_category()
    {
        $category = Category::factory()->create();

        $data = [
            'name' => $this->faker->word,
            'parent_id' => null
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->putJson("/api/v1/admin/categories/{$category->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => ['id', 'name', 'slug', 'parent_id', 'created_at', 'updated_at']
                 ]);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => $data['name']]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_delete_a_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->deleteJson("/api/v1/admin/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message'
                 ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
