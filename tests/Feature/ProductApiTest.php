<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products(): void
    {
        Product::create([
            'name' => 'Keyboard',
            'price' => '1590.00',
            'stock' => 10,
        ]);

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Keyboard']);
    }

    public function test_can_create_product(): void
    {
        $this->postJson('/api/products', [
            'name' => 'Mouse',
            'description' => 'Wireless Mouse',
            'price' => '590.00',
            'stock' => 5,
        ])
            ->assertCreated()
            ->assertJsonPath('name', 'Mouse');

        $this->assertDatabaseHas('products', [
            'name' => 'Mouse',
            'stock' => 5,
        ]);
    }

    public function test_can_get_product_by_id(): void
    {
        $product = Product::create([
            'name' => 'Monitor',
            'price' => '4990.00',
            'stock' => 3,
        ]);

        $this->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('name', 'Monitor');
    }

    public function test_returns_404_for_missing_product(): void
    {
        $this->getJson('/api/products/99999')
            ->assertNotFound();
    }

    public function test_can_patch_product(): void
    {
        $product = Product::create([
            'name' => 'Keyboard',
            'price' => '1590.00',
            'stock' => 10,
        ]);

        $this->patchJson("/api/products/{$product->id}", [
            'price' => '1390.00',
            'stock' => 15,
        ])
            ->assertOk()
            ->assertJsonPath('stock', 15)
            ->assertJsonPath('price', '1390.00');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 15,
        ]);
    }

    public function test_can_put_product(): void
    {
        $product = Product::create([
            'name' => 'Keyboard',
            'price' => '1590.00',
            'stock' => 10,
        ]);

        $this->putJson("/api/products/{$product->id}", [
            'name' => 'Keyboard V2',
            'description' => 'Updated Keyboard',
            'price' => '1490.00',
            'stock' => 20,
        ])
            ->assertOk()
            ->assertJsonPath('name', 'Keyboard V2')
            ->assertJsonPath('stock', 20);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::create([
            'name' => 'Keyboard',
            'price' => '1590.00',
            'stock' => 10,
        ]);

        $this->deleteJson("/api/products/{$product->id}")
            ->assertOk();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);

        $this->getJson("/api/products/{$product->id}")
            ->assertNotFound();
    }

    public function test_rejects_invalid_product(): void
    {
        $this->postJson('/api/products', [
            'price' => -100,
            'stock' => -5,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'price',
                'stock',
            ]);

        $this->assertDatabaseCount('products', 0);
    }
}
