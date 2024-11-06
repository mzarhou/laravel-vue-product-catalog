<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface
{
    /**
     * Get all root categories with their children
     *
     * @return Collection<int, Category>
     */
    public function getAllWithHierarchy(): Collection;

    /**
     * Create a new category
     *
     * @param array{
     *   name: string,
     *   parent_id?: int|null
     * } $data
     */
    public function create(array $data): Category;
}
