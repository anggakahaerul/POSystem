<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of branches.
     */
    public function index(Request $request)
    {
        $query = Branch::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $branches = $query->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('branches.form', [
            'branch' => null,
        ]);
    }

    /**
     * Store a newly created branch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:branches,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'npwp' => 'nullable|string|max:30',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'footer_note' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Kode cabang wajib diisi.',
            'code.unique' => 'Kode cabang sudah digunakan.',
            'name.required' => 'Nama cabang wajib diisi.',
            'logo.max' => 'Ukuran logo maksimal 1MB.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['code'] = strtoupper($validated['code']);

        // Handle logo upload - save as base64
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $validated['logo'] = $file->getClientOriginalName();
            $validated['logo_base64'] = 'data:image/' . $file->getClientOriginalExtension() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        Branch::create($validated);

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the branch.
     */
    public function edit(Branch $branch)
    {
        return view('branches.form', compact('branch'));
    }

    /**
     * Update the branch.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'npwp' => 'nullable|string|max:30',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'footer_note' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['code'] = strtoupper($validated['code']);

        // Handle logo upload - save as base64
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $validated['logo'] = $file->getClientOriginalName();
            $validated['logo_base64'] = 'data:image/' . $file->getClientOriginalExtension() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        $branch->update($validated);

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    /**
     * Remove the branch.
     */
    public function destroy(Branch $branch)
    {
        if ($branch->purchaseOrders()->count() > 0) {
            return redirect()->route('branches.index')
                ->with('error', 'Cabang masih memiliki PO, tidak dapat dihapus.');
        }

        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }
}
