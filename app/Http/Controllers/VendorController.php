<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorRequest;
use App\Models\PaymentTerm;
use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct(
        protected VendorService $vendorService
    ) {}

    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $vendors = $this->vendorService->getPaginatedList($filters, 10);
        $suggestedCode = $this->vendorService->generateCode();
        $paymentTerms = PaymentTerm::active()->orderBy('days')->get();

        return view('vendors.index', [
            'vendors' => $vendors,
            'suggestedCode' => $suggestedCode,
            'paymentTerms' => $paymentTerms,
            'moduleName' => 'Vendor',
            'moduleRoute' => 'vendors',
        ]);
    }

    /**
     * Store a newly created vendor.
     */
    public function store(VendorRequest $request)
    {
        $vendor = $this->vendorService->createVendor($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor berhasil ditambahkan.',
                'data' => $vendor,
            ]);
        }

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    /**
     * Display the vendor details (or JSON for edit modal).
     */
    public function show(Vendor $vendor, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $vendor,
            ]);
        }

        $vendor->loadCount('purchaseOrders');
        $vendor->load('paymentTerm');

        return view('vendors.show', [
            'vendor' => $vendor,
            'moduleName' => 'Vendor',
            'moduleRoute' => 'vendors',
        ]);
    }

    /**
     * Show form for editing.
     */
    public function edit(Vendor $vendor, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $vendor,
            ]);
        }

        return view('vendors.form', [
            'vendor' => $vendor,
            'suggestedCode' => null,
            'moduleName' => 'Vendor',
            'moduleRoute' => 'vendors',
        ]);
    }

    /**
     * Update the vendor.
     */
    public function update(VendorRequest $request, Vendor $vendor)
    {
        $updated = $this->vendorService->updateVendor($vendor->id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor berhasil diperbarui.',
                'data' => $updated,
            ]);
        }

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    /**
     * Remove the vendor.
     */
    public function destroy(Vendor $vendor, Request $request)
    {
        $result = $this->vendorService->deleteVendor($vendor->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->route('vendors.index')->with('error', $result['message']);
        }

        return redirect()->route('vendors.index')->with('success', $result['message']);
    }
}
