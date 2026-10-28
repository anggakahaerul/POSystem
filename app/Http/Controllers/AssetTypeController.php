<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetTypeRequest;
use App\Models\AssetType;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetTypeController extends Controller
{
    /**
     * Tampilkan daftar master Tipe Asset
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = AssetType::withCount('categories')->orderBy('code');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', $status == '1');
        }

        $assetTypes = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => AssetType::count(),
            'active' => AssetType::where('is_active', true)->count(),
            'linked_categories' => Category::whereNotNull('asset')->count(),
        ];

        return view('asset-types.index', compact('assetTypes', 'stats', 'search', 'status'));
    }

    /**
     * Simpan Tipe Asset baru
     */
    public function store(AssetTypeRequest $request)
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);
        $data['badge_color'] = $data['badge_color'] ?? 'info';

        $assetType = AssetType::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipe asset berhasil ditambahkan.',
                'data' => $assetType,
            ]);
        }

        return redirect()->route('asset-types.index')
            ->with('success', 'Tipe asset berhasil ditambahkan.');
    }

    /**
     * Ambil data single Tipe Asset untuk modal edit
     */
    public function show(AssetType $assetType): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $assetType,
        ]);
    }

    /**
     * Update Tipe Asset
     */
    public function update(AssetTypeRequest $request, AssetType $assetType)
    {
        $oldCode = $assetType->code;
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);
        $data['badge_color'] = $data['badge_color'] ?? 'info';

        $assetType->update($data);

        // Jika kode berubah, perbarui kategori yang terhubung
        if ($oldCode !== $data['code']) {
            Category::where('asset', $oldCode)->update(['asset' => $data['code']]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipe asset berhasil diperbarui.',
                'data' => $assetType,
            ]);
        }

        return redirect()->route('asset-types.index')
            ->with('success', 'Tipe asset berhasil diperbarui.');
    }

    /**
     * Hapus Tipe Asset
     */
    public function destroy(AssetType $assetType)
    {
        $linkedCategories = Category::where('asset', $assetType->code)->count();

        if ($linkedCategories > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tidak dapat menghapus tipe asset '{$assetType->code}' karena masih digunakan oleh {$linkedCategories} kategori.",
            ], 422);
        }

        $assetType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipe asset berhasil dihapus.',
        ]);
    }

    /**
     * API untuk mengambil daftar Tipe Asset aktif (bisa dipakai Select2 / AJAX)
     */
    public function getActiveList(): JsonResponse
    {
        $types = AssetType::active()->orderBy('name')->get(['id', 'code', 'name', 'badge_color']);
        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }
}
