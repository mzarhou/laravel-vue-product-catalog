<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface extends RepositoryInterface
{
    public function findWithCategories(int $id): ?Product;

    /**
     * Get paginated products with optional filters
     *
     * @param array{
     *  category_id?: int,
     *  sort_price?: string,
     *  per_page?: int
     * } $filters
     */
    public function getPaginatedWithFilters(array $filters = []): LengthAwarePaginator;

    /**
     * Sync product categories
     */
    public function syncCategories(int $productId, array $categoryIds): bool;
}
