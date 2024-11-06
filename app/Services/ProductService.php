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
        if (! isset($data['image'])) {
            throw new \RuntimeException('Product image is required.');
        }

        $productData = [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => (float) $data['price'],
        ];

        // Handle image upload
        if ($data['image'] instanceof UploadedFile) {
            $productData['image'] = $this->handleImageUpload($data['image']);
        } elseif (is_string($data['image'])) {
            // For CLI: image path is already processed and stored
            $productData['image'] = $data['image'];
        } else {
            throw new \RuntimeException('Invalid image format provided.');
        }

        try {
            /** @var Product $product */
            $product = $this->productRepository->create($productData);

            if (isset($data['categories'])) {
                $this->productRepository->syncCategories(
                    $product->id,
                    $data['categories']
                );
                $product->load('categories');
            }

            return $product;
        } catch (\Exception $e) {
            // Clean up the uploaded image if product creation fails
            if (isset($productData['image'])) {
                Storage::disk('public')->delete($productData['image']);
            }
            throw $e;
        }
    }

    public function getPaginatedWithFilters(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getPaginatedWithFilters($filters)
            ->through(function (Product $product) {
                $product->image_url = $product->getImageUrl();

                return $product;
            });
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
            Storage::disk('public')->delete($product->image);
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
}
