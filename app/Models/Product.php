<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withTimestamps();
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2);
    }

    /**
     * Get full image URL
     */
    public function getImageUrl(): string
    {
        return asset('storage/'.$this->image);
    }

    /**
     * Get all categories including parent categories
     *
     * @return Collection<int, Category>
     */
    public function getAllCategories(): Collection
    {
        $categories = $this->categories;
        $ancestors = new Collection;

        foreach ($categories as $category) {
            $ancestors = $ancestors->merge($category->getAncestors());
        }

        return $categories->merge($ancestors)->unique('id');
    }
}
