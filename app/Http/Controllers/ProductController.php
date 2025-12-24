<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected string $moduleName = 'Produk';
    protected string $moduleRoute = 'products';

    /**
     * Display a listing of products with pagination.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $products = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $suggestedSku = Product::generateSku();

        return view('products.form', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'product' => null,
            'categories' => $categories,
            'suggestedSku' => $suggestedSku,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products',
            'unit' => 'required|string|max:20',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'name.required' => 'Nama produk wajib diisi.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah digunakan.',
            'unit.required' => 'Unit wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
        ]);

        // Clean price format (remove dots)
        $validated['price'] = str_replace(['.', ','], '', $validated['price']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_stock'] = $validated['min_stock'] ?? 0;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the product.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();

        return view('products.form', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'product' => $product,
            'categories' => $categories,
            'suggestedSku' => null,
        ]);
    }

    /**
     * Update the product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'unit' => 'required|string|max:20',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['price'] = str_replace(['.', ','], '', $validated['price']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_stock'] = $validated['min_stock'] ?? 0;

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the product.
     */
    public function destroy(Product $product)
    {
        if ($product->purchaseOrderDetails()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Tidak dapat menghapus produk yang sudah digunakan dalam PO.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * API: Get single product data (for AJAX in PO form)
     */
    public function getProduct(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'unit' => $product->unit,
            'price' => $product->price,
            'stock' => $product->stock,
            'formatted_price' => $product->formatted_price,
        ]);
    }

    /**
     * API: Get products list (for AJAX Select2 in PO form)
     */
    public function getProducts(Request $request)
    {
        $search = $request->get('q', '');
        
        $products = Product::active()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'sku', 'unit', 'price', 'stock']);

        return response()->json($products->map(fn($p) => [
            'id' => $p->id,
            'text' => "{$p->sku} - {$p->name}",
            'name' => $p->name,
            'sku' => $p->sku,
            'unit' => $p->unit,
            'price' => $p->price,
            'stock' => $p->stock,
        ]));
    }
}
