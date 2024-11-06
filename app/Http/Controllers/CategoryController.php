<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\View\View;

class CategoryController extends Controller
{
    private CategoryServiceInterface $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function create(): View
    {
        $categories = $this->categoryService->getAllWithHierarchy();

        return view('categories.create', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->create($request->validated());

        return redirect()
            ->route('categories.create')
            ->with('success', 'Category created successfully.');
    }
}
