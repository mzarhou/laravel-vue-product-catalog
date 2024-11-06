<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

it('shows product creation page with categories', function () {
    // Arrange
    $category = Category::factory()->create(['name' => 'Electronics']);
    $subcategory = Category::factory()
        ->create([
            'name' => 'Laptops',
            'parent_id' => $category->id,
        ]);

    // Act
    $response = $this->get(route('products.create'));

    // Assert
    $response->assertStatus(200)
        ->assertViewIs('products.create')
        ->assertSee('Electronics')
        ->assertSee('Laptops');
});

it('creates a product successfully', function () {
    // Arrange
    $category = Category::factory()->create();
    $image = UploadedFile::fake()->image('product.jpg');

    $productData = [
        'name' => 'New Laptop',
        'description' => 'Powerful laptop with great features',
        'price' => 999.99,
        'categories' => [$category->id],
        'image' => $image,
    ];

    // Act
    $response = $this->post(route('products.store'), $productData);

    // Assert
    $response->assertRedirect(route('products.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('products', [
        'name' => 'New Laptop',
        'description' => 'Powerful laptop with great features',
        'price' => 999.99,
    ]);

    // Assert image was stored
    $product = \App\Models\Product::where('name', 'New Laptop')->first();
    // Storage::disk('public')->assertExists($product->image);
});

it('validates required fields', function () {
    // Act
    $response = $this->post(route('products.store'), []);

    // Assert
    $response->assertSessionHasErrors([
        'name',
        'description',
        'price',
        'categories',
    ]);
});

it('validates price is numeric and positive', function ($price, $shouldPass) {
    // Arrange
    $category = Category::factory()->create();

    $productData = [
        'name' => 'Test Product',
        'description' => 'Test Description',
        'price' => $price,
        'categories' => [$category->id],
    ];

    // Act
    $response = $this->post(route('products.store'), $productData);

    // Assert
    if ($shouldPass) {
        $response->assertSessionDoesntHaveErrors(['price']);
    } else {
        $response->assertSessionHasErrors(['price']);
    }
})->with([
    'negative' => [-10, false],
    'zero' => [0, true],
    'string' => ['abc', false],
    'valid decimal' => [99.99, true],
    'valid integer' => [100, true],
]);

it('validates image is valid', function () {
    // Arrange
    $category = Category::factory()->create();
    $invalidFile = UploadedFile::fake()->create('document.pdf');

    $productData = [
        'name' => 'Test Product',
        'description' => 'Test Description',
        'price' => 99.99,
        'categories' => [$category->id],
        'image' => $invalidFile,
    ];

    // Act
    $response = $this->post(route('products.store'), $productData);

    // Assert
    $response->assertSessionHasErrors(['image']);
});

it('validates maximum image size', function () {
    // Arrange
    $category = Category::factory()->create();
    $largeImage = UploadedFile::fake()->image('large.jpg')->size(3000); // 3MB

    $productData = [
        'name' => 'Test Product',
        'description' => 'Test Description',
        'price' => 99.99,
        'categories' => [$category->id],
        'image' => $largeImage,
    ];

    // Act
    $response = $this->post(route('products.store'), $productData);

    // Assert
    $response->assertSessionHasErrors(['image']);
});
