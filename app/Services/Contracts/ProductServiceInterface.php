<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    /**
     * Create a new product with categories and image
     *
     * @param array{
     *   name: string,
     *   description: string,
     *   price: float|string,
     *   categories: array<int>,
     *   image?: UploadedFile
     * } $data
     */
    public function create(array $data): Product;

    /**
     * Get paginated products with filters
     *
     * @param array{
     *   category_id?: int,
     *   sort_price?: string,
     *   per_page?: int
     * } $filters
     */
    public function getPaginatedWithFilters(array $filters = []): LengthAwarePaginator;

    /**
     * Find product by ID with its categories
     */
    public function findWithCategories(int $id): ?Product;

    /**
     * Delete product and its image
     */
    public function delete(int $id): bool;
}
