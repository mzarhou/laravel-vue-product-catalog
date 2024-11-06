<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateProductCommand extends Command
{
    protected $signature = 'product:create
        {name : The name of the product}
        {description : The description of the product}
        {price : The price of the product (format: 99.99)}
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

            // Prepare data
            $data = [
                'name' => $this->argument('name'),
                'description' => $this->argument('description'),
                'price' => (float) $this->argument('price'),
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
            'categories' => $this->argument('categories'),
        ], [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['required', 'numeric', 'exists:categories,id'],
        ], [
            'price.numeric' => 'Price must be a valid number (e.g., 99.99)',
            'price.min' => 'Price must be greater than zero',
            'categories.required' => 'At least one category ID is required',
            'categories.*.numeric' => 'Category IDs must be numbers',
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

    private function displaySuccess($product): void
    {
        $this->info('Product created successfully!');
        $this->table(
            ['ID', 'Name', 'Price', 'Categories'],
            [[
                $product->id,
                $product->name,
                number_format($product->price, 2),
                $product->categories->pluck('name')->implode(', '),
            ]]
        );
    }
}
