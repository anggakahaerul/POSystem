<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    protected string $moduleName = 'Vendor';
    protected string $moduleRoute = 'vendors';

    /**
     * Display a listing of vendors with pagination.
     */
    public function index(Request $request)
    {
        $query = Vendor::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $vendors = $query->withCount('purchaseOrders')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('vendors.index', [
            'vendors' => $vendors,
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
        ]);
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $suggestedCode = Vendor::generateCode();

        return view('vendors.form', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'vendor' => null,
            'suggestedCode' => $suggestedCode,
        ]);
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:vendors',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'bank_info' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Kode vendor wajib diisi.',
            'code.unique' => 'Kode vendor sudah digunakan.',
            'name.required' => 'Nama vendor wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Vendor::create($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    /**
     * Display the vendor details.
     */
    public function show(Vendor $vendor)
    {
        $vendor->loadCount('purchaseOrders');

        return view('vendors.show', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'vendor' => $vendor,
        ]);
    }

    /**
     * Show the form for editing the vendor.
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.form', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'vendor' => $vendor,
            'suggestedCode' => null,
        ]);
    }

    /**
     * Update the vendor.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:vendors,code,' . $vendor->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'bank_info' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $vendor->update($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    /**
     * Remove the vendor.
     */
    public function destroy(Vendor $vendor)
    {
        if ($vendor->purchaseOrders()->count() > 0) {
            return redirect()->route('vendors.index')
                ->with('error', 'Tidak dapat menghapus vendor yang memiliki riwayat PO.');
        }

        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }
}
