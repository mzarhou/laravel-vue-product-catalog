<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old images
        Storage::disk('public')->deleteDirectory('products');

        // Create main categories
        $electronics = Category::factory()->create(['name' => 'Electronics']);
        $clothing = Category::factory()->create(['name' => 'Clothing']);
        $books = Category::factory()->create(['name' => 'Books']);
        $sports = Category::factory()->create(['name' => 'Sports & Outdoors']);

        // Create subcategories for Electronics
        $laptops = Category::factory()->asChild($electronics)->create(['name' => 'Laptops']);
        $smartphones = Category::factory()->asChild($electronics)->create(['name' => 'Smartphones']);
        $accessories = Category::factory()->asChild($electronics)->create(['name' => 'Accessories']);

        // Create subcategories for Clothing
        $mens = Category::factory()->asChild($clothing)->create(['name' => "Men's Clothing"]);
        $womens = Category::factory()->asChild($clothing)->create(['name' => "Women's Clothing"]);
        $kids = Category::factory()->asChild($clothing)->create(['name' => "Kids' Clothing"]);

        // Create subcategories for Books
        $fiction = Category::factory()->asChild($books)->create(['name' => 'Fiction']);
        $nonFiction = Category::factory()->asChild($books)->create(['name' => 'Non-Fiction']);
        $technical = Category::factory()->asChild($books)->create(['name' => 'Technical']);

        // Create subcategories for Sports
        $fitness = Category::factory()->asChild($sports)->create(['name' => 'Fitness']);
        $camping = Category::factory()->asChild($sports)->create(['name' => 'Camping']);
        $swimming = Category::factory()->asChild($sports)->create(['name' => 'Swimming']);

        // Create products for each category with different price ranges and some with images
        $this->createProductsForCategory($laptops, 5);
        $this->createProductsForCategory($smartphones, 5);
        $this->createProductsForCategory($accessories, 8);

        $this->createProductsForCategory($mens, 6);
        $this->createProductsForCategory($womens, 6);
        $this->createProductsForCategory($kids, 4);

        $this->createProductsForCategory($fiction, 5);
        $this->createProductsForCategory($nonFiction, 5);
        $this->createProductsForCategory($technical, 3);

        $this->createProductsForCategory($fitness, 4);
        $this->createProductsForCategory($camping, 4);
        $this->createProductsForCategory($swimming, 3);
    }

    private function createProductsForCategory(Category $category, int $count): void
    {
        // Create products with different price ranges
        $priceRanges = ['lowPrice', 'mediumPrice', 'highPrice'];

        for ($i = 0; $i < $count; $i++) {
            // Randomly decide if product should have an image (70% chance)
            $withImage = rand(1, 10) <= 7;

            // Create product with random price range
            $product = Product::factory()
                ->{$priceRanges[array_rand($priceRanges)]}()
                ->when($withImage, fn ($factory) => $factory->withImage())
                ->create();

            // Attach main category and randomly attach related categories
            $product->categories()->attach($category->id);

            // 30% chance to attach parent category if exists
            if ($category->parent_id && rand(1, 10) <= 3) {
                $product->categories()->attach($category->parent_id);
            }

            // 20% chance to attach a random sibling category
            if (rand(1, 10) <= 2) {
                $siblingCategories = Category::where('parent_id', $category->parent_id)
                    ->where('id', '!=', $category->id)
                    ->pluck('id');

                if ($siblingCategories->isNotEmpty()) {
                    $product->categories()->attach(
                        $siblingCategories->random()
                    );
                }
            }
        }
    }
}
