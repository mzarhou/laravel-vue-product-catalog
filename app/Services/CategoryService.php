<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllWithHierarchy(): Collection
    {
        return $this->categoryRepository
            ->getRootCategories()
            ->load('children');
    }

    public function create(array $data): Category
    {
        if (isset($data['parent_id'])) {
            $parent = $this->categoryRepository->find($data['parent_id']);
            if (! $parent) {
                throw new \InvalidArgumentException('Parent category not found');
            }
        }

        /** @var Category */
        return $this->categoryRepository->create($data);
    }
}
