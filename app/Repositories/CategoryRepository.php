<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function getRootCategories(): Collection
    {
        return $this->model->whereNull('parent_id')->get();
    }

    public function findWithChildren(int $id): ?Category
    {
        return $this->model->with('children')->find($id);
    }
}
