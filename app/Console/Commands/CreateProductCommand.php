<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CreateProductCommand extends Command
{
    protected $signature = 'product:create
        {name : The name of the product}
        {description : The description of the product}
        {price : The price of the product (format: 99.99)}
        {image : Path to the product image}
        {categories* : The category IDs (space separated)}';

    protected $description = 'Create a new product from command line';

    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        parent::__construct();
        $this->productService = $productService;
    }

    public function handle(): int
    {
        try {
            // Validate input
            if (! $this->validateInput()) {
                return 1;
            }

            // Process image
            $imagePath = $this->processImage();
            if (! $imagePath) {
                return 1;
            }

            // Prepare data
            $data = [
                'name' => $this->argument('name'),
                'description' => $this->argument('description'),
                'price' => (float) $this->argument('price'),
                'image' => $imagePath,
                'categories' => array_map('intval', $this->argument('categories')),
            ];

            // Create product
            DB::beginTransaction();
            try {
                $product = $this->productService->create($data);
                DB::commit();

                $this->displaySuccess($product);

                return 0;
            } catch (\Exception $e) {
                DB::rollBack();
                Storage::disk('public')->delete($imagePath);
                throw $e;
            }

        } catch (\Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());

            return 1;
        }
    }

    private function validateInput(): bool
    {
        $validator = Validator::make([
            'name' => $this->argument('name'),
            'description' => $this->argument('description'),
            'price' => $this->argument('price'),
            'image' => $this->argument('image'),
            'categories' => $this->argument('categories'),
        ], [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'image' => ['required', 'string'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'numeric', 'exists:categories,id'],
        ], [
            'price.numeric' => 'Price must be a valid number (e.g., 99.99)',
            'price.min' => 'Price must be greater than zero',
            'image.required' => 'Image path is required',
            'categories.required' => 'At least one category ID is required',
            'categories.*.exists' => 'One or more category IDs do not exist',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return false;
        }

        return true;
    }

    private function processImage(): ?string
    {
        $imagePath = $this->argument('image');

        if (! File::exists($imagePath)) {
            $this->error("Image file not found: {$imagePath}");

            return null;
        }

        if (! in_array(File::extension($imagePath), ['jpg', 'jpeg', 'png', 'gif'])) {
            $this->error('Invalid image format. Supported formats: jpg, jpeg, png, gif');

            return null;
        }

        $fileSize = File::size($imagePath);
        if ($fileSize > 2 * 1024 * 1024) { // 2MB
            $this->error('Image size must not exceed 2MB');

            return null;
        }

        try {
            $storagePath = Storage::disk('public')->putFile(
                'products',
                $imagePath
            );

            if (! $storagePath) {
                throw new \RuntimeException('Failed to store image');
            }

            return $storagePath;
        } catch (\Exception $e) {
            $this->error('Failed to process image: '.$e->getMessage());

            return null;
        }
    }

    private function displaySuccess($product): void
    {
        $this->info('Product created successfully!');
        $this->table(
            ['ID', 'Name', 'Price', 'Image', 'Categories'],
            [[
                $product->id,
                $product->name,
                number_format($product->price, 2),
                $product->getImageUrl(),
                $product->categories->pluck('name')->implode(', '),
            ]]
        );
    }
}
