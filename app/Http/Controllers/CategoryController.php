<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected string $moduleName = 'Kategori';
    protected string $moduleRoute = 'categories';

    /**
     * Display a listing of categories with pagination.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        // Filter by asset
        if ($request->filled('asset')) {
            $query->where('asset', $request->asset);
        }

        $categories = $query->withCount('products')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', [
            'categories' => $categories,
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.form', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'category' => null,
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'asset' => 'nullable|in:HRGA,MIS',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
            'asset.in' => 'Pilihan Asset harus HRGA atau MIS.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the category.
     */
    public function edit(Category $category)
    {
        return view('categories.form', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'category' => $category,
        ]);
    }

    /**
     * Update the category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'asset' => 'nullable|in:HRGA,MIS',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
            'asset.in' => 'Pilihan Asset harus HRGA atau MIS.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the category.
     */
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Tidak dapat menghapus kategori yang memiliki produk.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
