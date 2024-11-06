<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\CategoryService;
use App\Services\Contracts\CategoryServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
    }
}
