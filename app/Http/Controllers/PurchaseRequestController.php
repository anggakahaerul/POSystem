<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of purchase requests.
     */
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['creator', 'purchaseOrder.vendor', 'purchaseOrders.vendor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pr_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchaseRequests = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => PurchaseRequest::count(),
            'pending' => PurchaseRequest::where('status', 'pending')->count(),
            'processed' => PurchaseRequest::where('status', 'processed')->count(),
        ];

        return view('purchase-requests.index', compact('purchaseRequests', 'stats'));
    }

    /**
     * Show the form for creating a new purchase request.
     */
    public function create()
    {
        return view('purchase-requests.create');
    }

    /**
     * Store a newly created purchase request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
        ], [
            'title.required' => 'Judul request wajib diisi.',
            'attachment.mimes' => 'Lampiran harus berupa file PDF atau gambar (PNG/JPG).',
            'attachment.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $path = null;
        $originalName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('purchase_requests', 'public');
        }

        PurchaseRequest::create([
            'pr_number' => PurchaseRequest::generatePrNumber(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'attachment' => $path,
            'attachment_name' => $originalName,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('purchase-requests.index')
            ->with('success', 'Permintaan Pembelian berhasil dibuat.');
    }

    /**
     * Display the specified purchase request.
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['creator', 'purchaseOrders.vendor', 'purchaseOrders.branch', 'purchaseOrder.vendor']);

        $availablePurchaseOrders = PurchaseOrder::where(function ($q) use ($purchaseRequest) {
                $q->whereNull('purchase_request_id')
                  ->orWhere('purchase_request_id', '!=', $purchaseRequest->id);
            })
            ->with(['vendor', 'branch'])
            ->latest()
            ->take(50)
            ->get();

        return view('purchase-requests.show', compact('purchaseRequest', 'availablePurchaseOrders'));
    }

    /**
     * Show the form for editing the specified purchase request.
     */
    public function edit(PurchaseRequest $purchaseRequest)
    {
        return view('purchase-requests.edit', compact('purchaseRequest'));
    }

    /**
     * Update the specified purchase request in storage.
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
        ], [
            'title.required' => 'Judul request wajib diisi.',
            'attachment.mimes' => 'Lampiran harus berupa file PDF atau gambar (PNG/JPG).',
            'attachment.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('attachment')) {
            // Delete old file
            if ($purchaseRequest->attachment && Storage::disk('public')->exists($purchaseRequest->attachment)) {
                Storage::disk('public')->delete($purchaseRequest->attachment);
            }

            $file = $request->file('attachment');
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment'] = $file->store('purchase_requests', 'public');
        }

        $purchaseRequest->update($data);

        return redirect()->route('purchase-requests.index')
            ->with('success', 'Permintaan Pembelian berhasil diperbarui.');
    }

    /**
     * Remove the specified purchase request from storage.
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->purchaseOrders()->exists() || $purchaseRequest->purchaseOrder()->exists()) {
            return back()->with('error', 'Permintaan Pembelian tidak dapat dihapus karena sudah memiliki PO terkait.');
        }

        if ($purchaseRequest->attachment && Storage::disk('public')->exists($purchaseRequest->attachment)) {
            Storage::disk('public')->delete($purchaseRequest->attachment);
        }

        $purchaseRequest->delete();

        return redirect()->route('purchase-requests.index')
            ->with('success', 'Permintaan Pembelian berhasil dihapus.');
    }

    /**
     * Download or view the attachment.
     */
    public function downloadAttachment(PurchaseRequest $purchaseRequest)
    {
        if (!$purchaseRequest->attachment || !Storage::disk('public')->exists($purchaseRequest->attachment)) {
            abort(404, 'File attachment tidak ditemukan.');
        }

        return Storage::disk('public')->response(
            $purchaseRequest->attachment,
            $purchaseRequest->attachment_name ?? basename($purchaseRequest->attachment)
        );
    }

    /**
     * Attach an existing PO to this purchase request.
     */
    public function attachPo(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
        ], [
            'purchase_order_id.required' => 'Silakan pilih Purchase Order.',
            'purchase_order_id.exists' => 'Purchase Order tidak ditemukan.',
        ]);

        $po = PurchaseOrder::with(['vendor', 'branch'])->findOrFail($validated['purchase_order_id']);
        $po->purchase_request_id = $purchaseRequest->id;
        $po->save();

        if ($purchaseRequest->status !== 'processed') {
            $purchaseRequest->update(['status' => 'processed']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'PO ' . $po->po_number . ' berhasil dihubungkan ke PR.',
                'po' => [
                    'id' => $po->id,
                    'po_number' => $po->po_number,
                    'po_date' => $po->po_date ? \Carbon\Carbon::parse($po->po_date)->format('d M Y') : $po->created_at->format('d M Y'),
                    'vendor_name' => $po->vendor->name ?? '-',
                    'branch_name' => $po->branch->name ?? '-',
                    'total_amount' => 'Rp ' . number_format($po->grand_total ?? $po->total_amount ?? 0, 0, ',', '.'),
                    'status' => $po->status,
                    'status_label' => $po->status_label,
                    'status_badge' => $po->status_badge,
                    'show_url' => route('purchase-orders.show', $po->id),
                    'detach_url' => route('purchase-requests.detach-po', [$purchaseRequest->id, $po->id]),
                ],
                'total_count' => $purchaseRequest->purchaseOrders()->count(),
                'pr_status' => $purchaseRequest->fresh()->status,
                'pr_status_label' => $purchaseRequest->fresh()->status_label,
                'pr_status_badge' => $purchaseRequest->fresh()->status_badge,
            ]);
        }

        return back()->with('success', 'PO ' . $po->po_number . ' berhasil dihubungkan.');
    }

    /**
     * Detach a PO from this purchase request.
     */
    public function detachPo(Request $request, PurchaseRequest $purchaseRequest, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->purchase_request_id == $purchaseRequest->id) {
            $purchaseOrder->purchase_request_id = null;
            $purchaseOrder->save();
        }

        $remainingCount = $purchaseRequest->purchaseOrders()->count();
        if ($remainingCount === 0) {
            $purchaseRequest->update(['status' => 'pending']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'PO ' . $purchaseOrder->po_number . ' berhasil dilepas dari PR.',
                'remaining_count' => $remainingCount,
                'pr_status' => $purchaseRequest->fresh()->status,
                'pr_status_label' => $purchaseRequest->fresh()->status_label,
                'pr_status_badge' => $purchaseRequest->fresh()->status_badge,
            ]);
        }

        return back()->with('success', 'PO ' . $purchaseOrder->po_number . ' berhasil dilepas dari PR.');
    }
}
