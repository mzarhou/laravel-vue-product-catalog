<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function findWithCategories(int $id): ?Product
    {
        return $this->model->with('categories')->find($id);
    }

    public function getPaginatedWithFilters(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->with('categories');

        if (isset($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        if (isset($filters['sort_price'])) {
            $direction = strtolower($filters['sort_price']) === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $direction);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    public function syncCategories(int $productId, array $categoryIds): bool
    {
        $product = $this->find($productId);
        if (! $product) {
            return false;
        }

        $product->categories()->sync($categoryIds);

        return true;
    }
}
