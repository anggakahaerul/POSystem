<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\AssetType;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'status', 'asset']);
        $products = $this->productService->getPaginatedList($filters, 10);
        $categories = Category::active()->orderBy('name')->get();
        $assetTypes = AssetType::active()->orderBy('name')->get();
        $suggestedSku = $this->productService->generateSku();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'assetTypes' => $assetTypes,
            'suggestedSku' => $suggestedSku,
            'moduleName' => 'Produk',
            'moduleRoute' => 'products',
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan.',
                'data' => $product,
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Get single product data for edit modal or API.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Show edit (supports AJAX modal or legacy view).
     */
    public function edit(Product $product, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $product,
            ]);
        }

        $categories = Category::active()->orderBy('name')->get();

        return view('products.form', [
            'product' => $product,
            'categories' => $categories,
            'suggestedSku' => null,
            'moduleName' => 'Produk',
            'moduleRoute' => 'products',
        ]);
    }

    /**
     * Update the product.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $updated = $this->productService->updateProduct($product->id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui.',
                'data' => $updated,
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the product.
     */
    public function destroy(Product $product, Request $request)
    {
        $result = $this->productService->deleteProduct($product->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->route('products.index')->with('error', $result['message']);
        }

        return redirect()->route('products.index')->with('success', $result['message']);
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
