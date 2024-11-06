<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService implements ProductServiceInterface
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function create(array $data): Product
    {
        $productData = [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => (float) $data['price'],
        ];

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $productData['image'] = $this->handleImageUpload($data['image']);
        }

        /** @var Product $product */
        $product = $this->productRepository->create($productData);

        if (isset($data['categories'])) {
            $this->productRepository->syncCategories($product->id, $data['categories']);
        }

        return $product->fresh(['categories']);
    }

    public function getPaginatedWithFilters(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getPaginatedWithFilters($filters);
    }

    public function findWithCategories(int $id): ?Product
    {
        return $this->productRepository->findWithCategories($id);
    }

    public function delete(int $id): bool
    {
        $product = $this->productRepository->find($id);

        if (! $product) {
            return false;
        }

        if ($product->image) {
            $this->deleteImage($product->image);
        }

        return $this->productRepository->delete($id);
    }

    /**
     * Handle image upload
     *
     * @throws \RuntimeException
     */
    private function handleImageUpload(UploadedFile $file): string
    {
        $path = $file->store('products', 'public');

        if ($path === false) {
            throw new \RuntimeException('Failed to upload image');
        }

        return $path;
    }

    /**
     * Delete product image
     */
    private function deleteImage(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
