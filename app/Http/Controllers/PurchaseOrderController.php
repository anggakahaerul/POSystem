<?php

namespace App\Http\Controllers;

use App\Mail\POApprovalRequest;
use App\Models\Branch;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderController extends Controller
{
    protected string $moduleName = 'Purchase Order';
    protected string $moduleRoute = 'purchase-orders';

    /**
     * Display a listing of purchase orders with pagination.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['vendor', 'branch', 'creator']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function($v) use ($search) {
                      $v->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $vendors = Vendor::active()->orderBy('name')->get();

        return view('purchase-orders.index', [
            'purchaseOrders' => $purchaseOrders,
            'vendors' => $vendors,
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
        ]);
    }

    /**
     * Show the form for creating a new PO.
     */
    public function create()
    {
        $vendors = Vendor::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();
        $products = Product::with('category')->active()->orderBy('name')->get();

        return view('purchase-orders.create', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'vendors' => $vendors,
            'branches' => $branches,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created PO with details.
     */
    public function store(Request $request)
    {
        // Clean money format from prices before validation
        $products = $request->input('products', []);
        foreach ($products as $key => $product) {
            if (isset($product['unit_price'])) {
                $products[$key]['unit_price'] = str_replace(['.', ','], '', $product['unit_price']);
            }
            if (isset($product['discount_value'])) {
                $products[$key]['discount_value'] = str_replace(['.', ','], '', $product['discount_value']);
            }
        }
        $request->merge(['products' => $products]);

        // Clean document-level discount
        if ($request->filled('discount_value')) {
            $request->merge(['discount_value' => str_replace(['.', ','], '', $request->discount_value)]);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'branch_id' => 'required|exists:branches,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'required_date' => 'nullable|date|after_or_equal:today',
            'note' => 'nullable|string',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.discount_type' => 'nullable|in:percentage,fixed',
            'products.*.discount_value' => 'nullable|numeric|min:0',
        ], [
            'vendor_id.required' => 'Silakan pilih vendor.',
            'branch_id.required' => 'Silakan pilih cabang.',
            'products.required' => 'Minimal harus ada 1 produk.',
            'products.min' => 'Minimal harus ada 1 produk.',
        ]);

        try {
            DB::beginTransaction();

            // Generate PO Number with branch
            $poNumber = PurchaseOrder::generatePoNumber($validated['branch_id']);

            // Create PO
            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'vendor_id' => $validated['vendor_id'],
                'branch_id' => $validated['branch_id'],
                'payment_term_id' => $validated['payment_term_id'] ?? null,
                'required_date' => $validated['required_date'] ?? null,
                'note' => $validated['note'] ?? null,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'ppn_percentage' => $validated['ppn_percentage'] ?? 11,
                'status' => 'draft',
                'created_by' => Auth::id(),
                'subtotal' => 0,
                'discount_amount' => 0,
                'ppn_amount' => 0,
                'total_amount' => 0,
            ]);

            // Create PO Details with item-level discount
            foreach ($validated['products'] as $item) {
                $unitPrice = $item['unit_price'];
                $qty = $item['quantity'];
                $subtotal = $qty * $unitPrice;

                // Calculate item discount
                $discountType = $item['discount_type'] ?? null;
                $discountValue = $item['discount_value'] ?? 0;
                $discountAmount = 0;

                if ($discountType === 'percentage' && $discountValue > 0) {
                    $discountAmount = ($subtotal * $discountValue) / 100;
                } elseif ($discountType === 'fixed' && $discountValue > 0) {
                    $discountAmount = $discountValue;
                }

                $subtotalAfterDiscount = $subtotal - $discountAmount;

                PurchaseOrderDetail::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'subtotal_after_discount' => $subtotalAfterDiscount,
                ]);
            }

            // Recalculate totals (handles document discount + PPN)
            $po->recalculateTotal();

            DB::commit();

            return redirect()->route('purchase-orders.show', $po->id)
                ->with('success', 'Purchase Order berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the PO details.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['vendor', 'details.product', 'creator', 'approver']);

        return view('purchase-orders.show', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'po' => $purchaseOrder,
        ]);
    }

    /**
     * Show the form for editing the PO.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        // Can only edit draft POs
        if (!$purchaseOrder->canEdit()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'PO tidak dapat diedit karena sudah disubmit.');
        }

        $purchaseOrder->load('details.product');
        $vendors = Vendor::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();

        return view('purchase-orders.edit', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'po' => $purchaseOrder,
            'vendors' => $vendors,
            'products' => $products,
            'branches' => $branches,
        ]);
    }

    /**
     * Update the PO.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canEdit()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'PO tidak dapat diedit.');
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'required_date' => 'nullable|date',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Update PO
            $purchaseOrder->update([
                'vendor_id' => $validated['vendor_id'],
                'required_date' => $validated['required_date'] ?? null,
                'note' => $validated['note'] ?? null,
            ]);

            // Delete existing details
            $purchaseOrder->details()->delete();

            // Create new details
            $totalAmount = 0;
            foreach ($validated['products'] as $item) {
                $unitPrice = str_replace(['.', ','], '', $item['unit_price']);
                $subtotal = $item['quantity'] * $unitPrice;

                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('success', 'Purchase Order berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the PO.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canEdit()) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'PO tidak dapat dihapus karena sudah disubmit.');
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }

    /**
 * Submit PO for approval.
 */
public function submit(Request $request, PurchaseOrder $purchaseOrder)
{
    if (!$purchaseOrder->canSubmit()) {
        return redirect()->route('purchase-orders.show', $purchaseOrder->id)
            ->with('error', 'PO tidak dapat disubmit.');
    }

    // Save creator signature
    if ($request->filled('creator_signature')) {
        $purchaseOrder->creator_signature = $request->creator_signature;
        $purchaseOrder->save();
    }

    $purchaseOrder->submit();

    // Send email notification to managers if enabled
    $this->sendApprovalRequestEmail($purchaseOrder);

    return redirect()->route('purchase-orders.show', $purchaseOrder->id)
        ->with('success', 'PO berhasil disubmit untuk approval.');    }

    /**
     * Send approval request email to all managers
     */
    protected function sendApprovalRequestEmail(PurchaseOrder $po): void
    {
        // Check if notification is enabled
        if (!Setting::isEmailNotificationEnabled('submit')) {
            return;
        }

        try {
            // Apply email settings
            Setting::applyEmailConfig();

            // Get all users with Manager role
            $managers = User::role('Manager')->get();

            // Load PO relationships for email
            $po->load(['vendor', 'details.product', 'creator', 'paymentTerm', 'branch']);

            foreach ($managers as $manager) {
                if ($manager->email) {
                    Mail::to($manager->email)->send(new POApprovalRequest($po));
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the submit
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }
    }

    /**
 * Approve PO (Manager only).
 */
public function approve(Request $request, PurchaseOrder $purchaseOrder)
{
    if (!Auth::user()->canApprovePO()) {
        return redirect()->route('purchase-orders.show', $purchaseOrder->id)
            ->with('error', 'Anda tidak memiliki akses untuk approve PO.');
    }

    if (!$purchaseOrder->canApprove()) {
        return redirect()->route('purchase-orders.show', $purchaseOrder->id)
            ->with('error', 'PO tidak dapat di-approve.');
    }

    // Save approver signature
    if ($request->filled('approver_signature')) {
        $purchaseOrder->approver_signature = $request->approver_signature;
        $purchaseOrder->save();
    }

    $purchaseOrder->approve(Auth::id());

    return redirect()->route('purchase-orders.show', $purchaseOrder->id)
        ->with('success', 'PO berhasil di-approve!');    }

    /**
     * Reject PO with reason (Manager only).
     */
    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->canApprovePO()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'Anda tidak memiliki akses untuk reject PO.');
        }

        if (!$purchaseOrder->canApprove()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'PO tidak dapat di-reject.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $purchaseOrder->reject(Auth::id(), $request->rejection_reason);

        return redirect()->route('purchase-orders.show', $purchaseOrder->id)
            ->with('success', 'PO berhasil di-reject.');
    }

    /**
     * Receive goods for approved PO.
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canReceive()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'PO tidak dapat diproses untuk penerimaan barang.');
        }

        $type = $request->get('type', 'full');

        if ($type === 'items') {
            // Receive specific items with quantities
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*' => 'required|integer|min:0',
            ], [
                'items.required' => 'Minimal pilih 1 item untuk diterima.',
                'items.min' => 'Minimal pilih 1 item untuk diterima.',
            ]);

            // Filter out zero quantities
            $itemsToReceive = array_filter($validated['items'], fn($qty) => $qty > 0);

            if (empty($itemsToReceive)) {
                return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                    ->with('error', 'Tidak ada item yang dipilih untuk diterima.');
            }

            $purchaseOrder->receiveItems($itemsToReceive, Auth::id());
            $message = 'Penerimaan barang berhasil dicatat.';

        } elseif ($type === 'partial') {
            $purchaseOrder->partialReceive(Auth::id());
            $message = 'Penerimaan barang parsial berhasil dicatat.';
        } else {
            $purchaseOrder->receiveGoods(Auth::id());
            $message = 'Barang berhasil diterima seluruhnya.';
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder->id)
            ->with('success', $message);
    }

    /**
 * Print PO to PDF.
 */
public function print(PurchaseOrder $purchaseOrder)
{
    if ($purchaseOrder->status !== 'approved') {
        return redirect()->route('purchase-orders.show', $purchaseOrder->id)
            ->with('error', 'Hanya PO yang sudah approved yang bisa dicetak.');
    }

    $purchaseOrder->load(['vendor', 'details.product', 'creator', 'approver', 'paymentTerm', 'branch']);

    // Clean filename (replace / with -)
    $filename = str_replace('/', '-', $purchaseOrder->po_number) . '.pdf';

    // If DomPDF is installed, use it. Otherwise, show HTML
    if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('purchase-orders.print', [
            'po' => $purchaseOrder,
            'isPdf' => true,
        ]);
        return $pdf->download($filename);
    }

    // Fallback: show print-friendly view
    return view('purchase-orders.print', [
        'po' => $purchaseOrder,
        'isPdf' => false,
    ]);
}

    /**
     * Preview PO (show HTML in browser).
     */
    public function preview(PurchaseOrder $purchaseOrder)
    {
        // Allow preview for draft and approved
        if (!in_array($purchaseOrder->status, ['draft', 'approved'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'Preview hanya tersedia untuk PO draft atau approved.');
        }

        $purchaseOrder->load(['vendor', 'details.product', 'creator', 'approver', 'paymentTerm', 'branch']);

        return view('purchase-orders.print', [
            'po' => $purchaseOrder,
            'isPdf' => false,
        ]);
    }
}
