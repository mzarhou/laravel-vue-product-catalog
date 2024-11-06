<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->paragraphs(2, true),
            'price' => $this->faker->randomFloat(2, 9.99, 999.99),
            'image' => null,
        ];
    }

    public function withImage(): self
    {
        return $this->state(function (array $attributes) {
            // Create a fake image and store it
            $image = UploadedFile::fake()->image('product.jpg', 640, 480);
            $path = $image->store('products', 'public');

            return [
                'image' => $path,
            ];
        });
    }

    public function lowPrice(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->randomFloat(2, 9.99, 49.99),
            ];
        });
    }

    public function mediumPrice(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->randomFloat(2, 50.00, 199.99),
            ];
        });
    }

    public function highPrice(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->randomFloat(2, 200.00, 999.99),
            ];
        });
    }
}
