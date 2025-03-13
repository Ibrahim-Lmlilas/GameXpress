<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_list_product(): void
    {
        // Create an admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );

        // Authenticate as admin
        $this->actingAs($admin);

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
          $response->assertJsonCount($productsCount);
    }


        // public function test_admin_can_update_product(): void
        // {
        //     $admin = User::firstOrCreate(
        //         ['email' => 'admin@gmail.com'],
        //         ['name' => 'Admin', 'password' => bcrypt('password')]
        //     );
        //     $this->actingAs($admin);

        //     $category = Category::create([
        //         'name' => 'Test Category',
        //         'slug' => 'test-category',
        //     ]);

        //     $product = Product::create([
        //         'name' => 'Old Product',
        //         'slug' => 'old-product',
        //         'price' => 49.99,
        //         'stock' => 5,
        //         'status' => 'available',
        //         'category_id' => $category->id,
        //     ]);

        //     $updatedData = [
        //         'name' => 'Updated Product',
        //         'price' => 59.99,
        //         'stock' => 15,
        //     ];

        //     $response = $this->putJson("/api/v1/admin/products/{$product->id}", $updatedData);

        //     $response->assertStatus(200);
        //     $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $updatedData));
        // }

        // public function test_admin_can_delete_product(): void
        // {
        //     $admin = User::firstOrCreate(
        //         ['email' => 'admin@gmail.com'],
        //         ['name' => 'Admin', 'password' => bcrypt('password')]
        //     );
        //     $this->actingAs($admin);

        //     $category = Category::create([
        //         'name' => 'Test Category',
        //         'slug' => 'test-category',
        //     ]);

        //     $product = Product::create([
        //         'name' => 'Product to Delete',
        //         'slug' => 'product-to-delete',
        //         'price' => 39.99,
        //         'stock' => 20,
        //         'status' => 'available',
        //         'category_id' => $category->id,
        //     ]);

        //     $response = $this->deleteJson("/api/v1/admin/products/{$product->id}");

        //     $response->assertStatus(200);
        //     $this->assertDatabaseMissing('products', ['id' => $product->id]);
        // }
}
