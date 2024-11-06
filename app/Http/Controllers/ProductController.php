<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Services\Contracts\CategoryServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    private ProductServiceInterface $productService;

    private CategoryServiceInterface $categoryService;

    public function __construct(
        ProductServiceInterface $productService,
        CategoryServiceInterface $categoryService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['category_id', 'sort_price', 'per_page']);
        $products = $this->productService->getPaginatedWithFilters($filters)->appends(request()->query());
        $categories = $this->categoryService->getAllWithHierarchy();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = $this->categoryService->getAllWithHierarchy();

        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->productService->create($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }
}
