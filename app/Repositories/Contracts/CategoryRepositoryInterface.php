<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * Get root categories (without parent)
     *
     * @return Collection<int, Category>
     */
    public function getRootCategories(): Collection;

    /**
     * Get category with its children
     */
    public function findWithChildren(int $id): ?Category;
}
