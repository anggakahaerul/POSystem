<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\AssetType;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'asset']);
        $categories = $this->categoryService->getPaginatedList($filters, 10);
        $assetTypes = AssetType::active()->orderBy('name')->get();

        return view('categories.index', [
            'categories' => $categories,
            'assetTypes' => $assetTypes,
            'moduleName' => 'Kategori',
            'moduleRoute' => 'categories',
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan.',
                'data' => $category,
            ]);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Get single category data for edit modal.
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Show form for editing (supports both AJAX modal and legacy view).
     */
    public function edit(Category $category, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $category,
            ]);
        }

        return view('categories.form', [
            'category' => $category,
            'moduleName' => 'Kategori',
            'moduleRoute' => 'categories',
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $updated = $this->categoryService->updateCategory($category->id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui.',
                'data' => $updated,
            ]);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category, Request $request)
    {
        $result = $this->categoryService->deleteCategory($category->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->route('categories.index')->with('error', $result['message']);
        }

        return redirect()->route('categories.index')->with('success', $result['message']);
    }
}
