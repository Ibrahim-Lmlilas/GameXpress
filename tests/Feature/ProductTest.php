<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Product;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductTest extends TestCase
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
    public function test_admin_can_create_and_list_product(): void
    {
        // Authenticate as admin
        $this->actingAs($this->admin, 'sanctum');

        // Create a category
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create products
        $products = Product::factory()->count(10)->create([
            'category_id' => $category->id,
        ]);

        // Count the number of products in the database
        $productsCount = Product::count();

        // Make request to fetch products
        $response = $this->getJson('/api/v1/admin/products');

        // Check the response status
        $response->assertStatus(200);

        // Assert that the response contains the correct number of products
        $response->assertJsonCount($productsCount, 'data');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_can_update_product(): void
    {
        // Authenticate as admin
        $this->actingAs($this->admin, 'sanctum');

        // Create a category
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create a product
        $product = Product::create([
            'name' => 'Old Product',
            'slug' => 'old-product',
            'price' => 49.99,
            'stock' => 5,
            'status' => 'available',
            'category_id' => $category->id,
        ]);

        // Data to update the product
        $updatedData = [
            'name' => 'Updated Product',
            'price' => 59.99,
            'stock' => 15,
        ];

        // Make request to update the product
        $response = $this->putJson("/api/v1/admin/products/{$product->id}", $updatedData);

        // Check the response status
        $response->assertStatus(200);

        // Assert that the product was updated in the database
        $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $updatedData));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_admin_can_delete_product(): void
    {
        // Authenticate as admin
        $this->actingAs($this->admin, 'sanctum');

        // Create a category
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create a product
        $product = Product::create([
            'name' => 'Product to Delete',
            'slug' => 'product-to-delete',
            'price' => 39.99,
            'stock' => 20,
            'status' => 'available',
            'category_id' => $category->id,
        ]);

        // Make request to delete the product
        $response = $this->deleteJson("/api/v1/admin/products/{$product->id}");

        // Check the response status
        $response->assertStatus(200);

        // Assert that the product was deleted from the database
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
