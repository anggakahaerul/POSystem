<?php

namespace App\Http\Controllers;

use App\Mail\POApprovalRequest;
use App\Mail\POApproved;
use App\Mail\PORejected;
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
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    protected string $moduleName = 'Purchase Order';
    protected string $moduleRoute = 'purchase-orders';

    /**
     * Display a listing of purchase orders with pagination.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['vendor', 'branch', 'creator', 'approver', 'details.product']);

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
    public function create(Request $request)
    {
        $purchaseRequest = null;
        if ($request->filled('purchase_request_id')) {
            $purchaseRequest = \App\Models\PurchaseRequest::find($request->purchase_request_id);
        }

        $vendors = Vendor::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();
        $products = Product::with('category')->active()->orderBy('name')->get();

        return view('purchase-orders.create', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'vendors' => $vendors,
            'branches' => $branches,
            'products' => $products,
            'purchaseRequest' => $purchaseRequest,
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

        // Clean document-level discount & pph_amount
        if ($request->filled('discount_value')) {
            $request->merge(['discount_value' => str_replace(['.', ','], '', $request->discount_value)]);
        }
        if ($request->filled('pph_amount')) {
            $request->merge(['pph_amount' => str_replace(['.', ','], '', $request->pph_amount)]);
        }

        $validated = $request->validate([
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'vendor_id' => 'required|exists:vendors,id',
            'branch_id' => 'required|exists:branches,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'required_date' => 'nullable|date|after_or_equal:today',
            'note' => 'nullable|string',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
            'is_pph' => 'nullable|boolean',
            'pph_type' => 'nullable|string|max:50',
            'pph_percentage' => 'nullable|numeric|min:0|max:100',
            'pph_amount' => 'nullable|numeric|min:0',
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

            $isPph = $request->boolean('is_pph');

            // Create PO
            $po = PurchaseOrder::create([
                'purchase_request_id' => $validated['purchase_request_id'] ?? null,
                'po_number' => $poNumber,
                'vendor_id' => $validated['vendor_id'],
                'branch_id' => $validated['branch_id'],
                'payment_term_id' => $validated['payment_term_id'] ?? null,
                'required_date' => $validated['required_date'] ?? null,
                'note' => $validated['note'] ?? null,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'ppn_percentage' => $validated['ppn_percentage'] ?? 11,
                'is_pph' => $isPph,
                'pph_type' => $isPph ? ($validated['pph_type'] ?? null) : null,
                'pph_percentage' => $isPph ? ($validated['pph_percentage'] ?? 0) : 0,
                'pph_amount' => $isPph && in_array($validated['pph_type'] ?? '', ['custom_nominal', 'nominal']) ? (float)$request->input('pph_amount', 0) : 0,
                'status' => 'draft',
                'tax_review_status' => $isPph ? PurchaseOrder::TAX_REVIEW_PENDING : PurchaseOrder::TAX_REVIEW_VERIFIED,
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

                if ($discountType === 'percentage' && discountValue > 0) {
                    $discountAmount = ($subtotal * $discountValue) / 100;
                } elseif ($discountType === 'fixed' && discountValue > 0) {
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
                    'is_pph' => false,
                    'pph_type' => null,
                    'pph_percentage' => 0,
                    'pph_amount' => 0,
                ]);
            }

            // Recalculate totals (handles document discount + PPN)
            $po->recalculateTotal();

            // If created from Purchase Request, update PR status to processed
            if (!empty($validated['purchase_request_id'])) {
                \App\Models\PurchaseRequest::where('id', $validated['purchase_request_id'])->update([
                    'status' => 'processed',
                ]);
            }

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
        $purchaseOrder->load(['vendor', 'details.product', 'creator', 'approver', 'receiver', 'payments', 'purchaseRequest', 'taxReviewer', 'branch', 'department', 'revisions.user', 'revisedBy']);

        // Determine recommended approver based on ApprovalRule matrix
        $creator = $purchaseOrder->creator ?? auth()->user();
        $department = $purchaseOrder->department ?? $creator?->department;
        $branchId = $purchaseOrder->branch_id ?? $creator?->branch_id;
        $totalAmount = (float) $purchaseOrder->total_amount;

        $matchingRule = \App\Models\ApprovalRule::findMatch($department?->id, $totalAmount);
        $recommendedApprover = null;
        $recommendedReason = null;
        $requiredApproverLevel = null;
        $requesterLevel = null;

        if ($matchingRule && $creator) {
            $requesterLevel = \App\Models\ApprovalRule::determineRequesterLevel($creator, $department, $branchId);
            $requiredApproverLevel = $matchingRule->getRequiredApproverLevel($requesterLevel);

            if ($department && in_array(strtoupper($requiredApproverLevel), ['L3', 'L4', 'L5'])) {
                $recommendedApprover = $department->getManagerForBranch($branchId, $requiredApproverLevel);
            }

            $reqLabel = ($requesterLevel === 'STAFF') ? 'Staff / SPV' : 'Manager ' . $requesterLevel;
            $appLabel = \App\Models\ApprovalRule::formatLevelLabel($requiredApproverLevel);
            $recommendedReason = "Sesuai aturan '{$matchingRule->name}' (nominal {$matchingRule->amount_range_label}) & level pemohon {$reqLabel}, approver yang ditugaskan adalah {$appLabel}.";
        }

        $approvers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Manager', 'Admin', 'General Manager', 'Regional Manager']);
        })->whereNotNull('email')
          ->orderBy('name')
          ->get(['id', 'name', 'email']);

        // Ensure recommended approver is present in approvers list
        if ($recommendedApprover && !$approvers->contains('id', $recommendedApprover->id)) {
            $approvers->prepend($recommendedApprover);
        }

        $taxReviewers = User::where(function($query) {
            $query->whereHas('roles', function($q) {
                $q->whereIn('name', ['Tax', 'Admin']);
            })->orWhereHas('permissions', function($q) {
                $q->where('name', 'review tax');
            });
        })->whereNotNull('email')
          ->where('email', '!=', '')
          ->orderBy('name')
          ->get(['id', 'name', 'email']);

        return view('purchase-orders.show', [
            'moduleName' => $this->moduleName,
            'moduleRoute' => $this->moduleRoute,
            'po' => $purchaseOrder,
            'approvers' => $approvers,
            'taxReviewers' => $taxReviewers,
            'matchingRule' => $matchingRule,
            'recommendedApprover' => $recommendedApprover,
            'recommendedReason' => $recommendedReason,
            'requiredApproverLevel' => $requiredApproverLevel,
            'requesterLevel' => $requesterLevel,
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

        // Clean document-level discount & pph_amount
        if ($request->filled('discount_value')) {
            $request->merge(['discount_value' => str_replace(['.', ','], '', $request->discount_value)]);
        }
        if ($request->filled('pph_amount')) {
            $request->merge(['pph_amount' => str_replace(['.', ','], '', $request->pph_amount)]);
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'vendor_id' => 'required|exists:vendors,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'required_date' => 'nullable|date',
            'note' => 'nullable|string',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
            'is_pph' => 'nullable|boolean',
            'pph_type' => 'nullable|string|max:50',
            'pph_percentage' => 'nullable|numeric|min:0|max:100',
            'pph_amount' => 'nullable|numeric|min:0',
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

            $isPph = $request->boolean('is_pph');

            // Update PO
            $purchaseOrder->update([
                'branch_id' => $validated['branch_id'],
                'vendor_id' => $validated['vendor_id'],
                'payment_term_id' => $validated['payment_term_id'] ?? null,
                'required_date' => $validated['required_date'] ?? null,
                'note' => $validated['note'] ?? null,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'ppn_percentage' => $validated['ppn_percentage'] ?? 11,
                'is_pph' => $isPph,
                'pph_type' => $isPph ? ($validated['pph_type'] ?? null) : null,
                'pph_percentage' => $isPph ? ($validated['pph_percentage'] ?? 0) : 0,
                'pph_amount' => $isPph && in_array($validated['pph_type'] ?? '', ['custom_nominal', 'nominal']) ? (float)$request->input('pph_amount', 0) : 0,
                'tax_review_status' => $isPph ? PurchaseOrder::TAX_REVIEW_PENDING : PurchaseOrder::TAX_REVIEW_VERIFIED,
            ]);

            // Delete existing details
            $purchaseOrder->details()->delete();

            // Create new details
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
                } elseif ($discountType === 'fixed' && discountValue > 0) {
                    $discountAmount = $discountValue;
                }

                $subtotalAfterDiscount = $subtotal - $discountAmount;

                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'subtotal_after_discount' => $subtotalAfterDiscount,
                    'is_pph' => false,
                    'pph_type' => null,
                    'pph_percentage' => 0,
                    'pph_amount' => 0,
                ]);
            }

            // Recalculate totals (handles discounts, PPN, and PPh)
            $purchaseOrder->recalculateTotal();

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
        if ($purchaseOrder->isWaitingTaxReview()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'PO ini mengenakan PPh dan perlu direview oleh Tim Tax terlebih dahulu sebelum dapat diajukan ke approval atasan.');
        }

        if (!$purchaseOrder->canSubmit()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'PO tidak dapat disubmit.');
        }

        $validated = $request->validate([
            'approver_email' => 'required|email',
            'creator_signature' => 'nullable|string',
        ], [
            'approver_email.required' => 'Alamat email atasan/approver wajib dipilih atau diisi.',
            'approver_email.email' => 'Format alamat email approver tidak valid.',
        ]);

        // Generate temporary secure approval token (64 characters) valid for 72 hours
        $purchaseOrder->approval_token = Str::random(64);
        $purchaseOrder->approver_email = $validated['approver_email'];
        $purchaseOrder->approval_token_expires_at = now()->addHours(72);

        // Save creator signature
        if ($request->filled('creator_signature')) {
            $purchaseOrder->creator_signature = $request->creator_signature;
        }
        $purchaseOrder->save();

        $purchaseOrder->submit();

        // Send email notification with direct approval magic link
        $this->sendApprovalRequestEmail($purchaseOrder);

        return redirect()->route('purchase-orders.show', $purchaseOrder->id)
            ->with('success', 'PO berhasil disubmit. Permohonan persetujuan telah dikirimkan ke email ' . $purchaseOrder->approver_email . '.');
    }

    /**
     * Revise PO (pull submitted/rejected PO back to draft for editing).
     */
    public function revise(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canRevise()) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'PO tidak dapat direvisi atau Anda tidak memiliki hak akses untuk merevisi PO ini.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'Alasan revisi PO wajib diisi.',
            'reason.max' => 'Alasan revisi maksimal 1000 karakter.',
        ]);

        $revised = $purchaseOrder->revise($validated['reason'], auth()->id());

        if (!$revised) {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('error', 'Gagal memproses penarikan revisi PO.');
        }

        return redirect()->route('purchase-orders.edit', $purchaseOrder->id)
            ->with('success', 'PO berhasil ditarik untuk Revisi ke-' . $purchaseOrder->revision_count . '. Silakan sesuaikan data item atau harga, simpan perubahan, lalu ajukan approval kembali.');
    }

    /**
     * Send approval request email to designated approver
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

            // Load PO relationships for email
            $po->load(['vendor', 'details.product', 'creator', 'paymentTerm', 'branch']);

            if (!empty($po->approver_email)) {
                Mail::to($po->approver_email)->send(new POApprovalRequest($po));
            } else {
                $managers = User::whereHas('roles', fn($q) => $q->where('name', 'Manager'))->get();
                foreach ($managers as $manager) {
                    if ($manager->email) {
                        Mail::to($manager->email)->send(new POApprovalRequest($po));
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the submit
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification to PO creator when approved
     */
    protected function sendApprovalNotificationEmail(PurchaseOrder $po): void
    {
        if (!Setting::isEmailNotificationEnabled('approve')) {
            return;
        }

        try {
            Setting::applyEmailConfig();
            $po->load(['creator', 'vendor', 'details.product', 'approver', 'paymentTerm', 'branch']);

            if ($po->creator && !empty($po->creator->email)) {
                Mail::to($po->creator->email)->send(new POApproved($po));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send PO approved email: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification to PO creator when rejected
     */
    protected function sendRejectionNotificationEmail(PurchaseOrder $po): void
    {
        if (!Setting::isEmailNotificationEnabled('reject')) {
            return;
        }

        try {
            Setting::applyEmailConfig();
            $po->load(['creator', 'vendor', 'details.product', 'approver', 'paymentTerm', 'branch']);

            if ($po->creator && !empty($po->creator->email)) {
                Mail::to($po->creator->email)->send(new PORejected($po));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send PO rejected email: ' . $e->getMessage());
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

        // Send notification email to creator
        $this->sendApprovalNotificationEmail($purchaseOrder);

        return redirect()->route('purchase-orders.show', $purchaseOrder->id)
            ->with('success', 'PO berhasil di-approve!');
    }

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

        // Send notification email to creator
        $this->sendRejectionNotificationEmail($purchaseOrder);

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

    $purchaseOrder->load(['vendor', 'details.product', 'creator', 'approver', 'paymentTerm', 'branch', 'purchaseRequest']);

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

        $purchaseOrder->load(['vendor', 'details.product', 'creator', 'approver', 'paymentTerm', 'branch', 'purchaseRequest']);

        return view('purchase-orders.print', [
            'po' => $purchaseOrder,
            'isPdf' => false,
        ]);
    }

    /**
     * Display the public external approval page (accessed via secure link in email)
     */
    public function externalApproval(string $token)
    {
        $po = PurchaseOrder::where('approval_token', $token)->first();

        if (!$po) {
            return view('purchase-orders.external-approval-status', [
                'type' => 'not_found',
                'title' => 'Tautan Tidak Ditemukan atau Sudah Digunakan',
                'message' => 'Tautan persetujuan ini sudah tidak valid atau PO telah selesai diproses.',
                'po' => null,
            ]);
        }

        if ($po->approval_token_expires_at && now()->greaterThan($po->approval_token_expires_at)) {
            return view('purchase-orders.external-approval-status', [
                'type' => 'expired',
                'title' => 'Tautan Persetujuan Kedaluwarsa',
                'message' => 'Masa berlaku tautan persetujuan ini telah berakhir pada ' . $po->approval_token_expires_at->format('d/m/Y H:i') . '. Silakan hubungi bagian purchasing untuk verifikasi ulang.',
                'po' => $po,
            ]);
        }

        if ($po->status !== PurchaseOrder::STATUS_PENDING) {
            return view('purchase-orders.external-approval-status', [
                'type' => 'already_processed',
                'title' => 'PO Telah Diproses',
                'message' => 'Purchase Order ini sudah berstatus: ' . strtoupper($po->status) . '. Tidak ada tindakan lanjutan yang diperlukan.',
                'po' => $po,
            ]);
        }

        $po->load(['vendor', 'details.product', 'creator', 'paymentTerm', 'branch']);

        return view('purchase-orders.external-approval', [
            'po' => $po,
            'token' => $token,
        ]);
    }

    /**
     * Preview print PDF directly for external approver
     */
    public function externalPreviewPdf(string $token)
    {
        $po = PurchaseOrder::where('approval_token', $token)->firstOrFail();
        $po->load(['vendor', 'details.product', 'creator', 'approver', 'paymentTerm', 'branch']);

        $filename = 'PO-' . str_replace('/', '-', $po->po_number) . '.pdf';

        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('purchase-orders.print', [
                'po' => $po,
                'isPdf' => true,
            ]);
            return $pdf->stream($filename);
        }

        return view('purchase-orders.print', [
            'po' => $po,
            'isPdf' => false,
        ]);
    }

    /**
     * Process external approval submission
     */
    public function processExternalApprove(Request $request, string $token)
    {
        $po = PurchaseOrder::where('approval_token', $token)->first();

        if (!$po || !$po->isApprovalTokenValid($token)) {
            return view('purchase-orders.external-approval-status', [
                'type' => 'invalid',
                'title' => 'Tautan Tidak Valid',
                'message' => 'Tautan persetujuan ini sudah kedaluwarsa atau sudah diproses.',
                'po' => $po,
            ]);
        }

        // Match approver to a user account if one exists with that email
        $approverUser = $po->approver_email ? User::where('email', $po->approver_email)->first() : null;
        $approverId = $approverUser ? $approverUser->id : null;

        // Save signature or file if uploaded or drawn
        if ($request->hasFile('approver_file')) {
            $file = $request->file('approver_file');
            $extension = strtolower($file->getClientOriginalExtension());
            if (in_array($extension, ['pdf', 'png', 'jpg', 'jpeg'])) {
                $path = $file->store('signatures/approvals', 'public');
                $po->approver_signature = $path;
                $po->save();
            }
        } elseif ($request->filled('approver_signature')) {
            $po->approver_signature = $request->approver_signature;
            $po->save();
        }

        $po->approve($approverId);

        // Send notification email to creator
        $this->sendApprovalNotificationEmail($po);

        return view('purchase-orders.external-approval-status', [
            'type' => 'success_approved',
            'title' => 'Persetujuan Berhasil Disimpan',
            'message' => 'Terima kasih. Purchase Order ' . $po->po_number . ' telah disetujui secara resmi. Bagian procurement akan segera memproses penerbitan pesanan ini ke vendor.',
            'po' => $po,
        ]);
    }

    /**
     * Process external reject submission
     */
    public function processExternalReject(Request $request, string $token)
    {
        $po = PurchaseOrder::where('approval_token', $token)->first();

        if (!$po || !$po->isApprovalTokenValid($token)) {
            return view('purchase-orders.external-approval-status', [
                'type' => 'invalid',
                'title' => 'Tautan Tidak Valid',
                'message' => 'Tautan ini sudah tidak aktif atau sudah diproses sebelumnya.',
                'po' => $po,
            ]);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan PO wajib dicantumkan.',
            'rejection_reason.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $approverUser = $po->approver_email ? User::where('email', $po->approver_email)->first() : null;
        $approverId = $approverUser ? $approverUser->id : null;

        $po->reject($approverId, $request->rejection_reason);

        // Send notification email to creator
        $this->sendRejectionNotificationEmail($po);

        return view('purchase-orders.external-approval-status', [
            'type' => 'success_rejected',
            'title' => 'PO Telah Ditolak',
            'message' => 'Purchase Order ' . $po->po_number . ' telah ditolak dengan catatan yang telah diteruskan ke staf purchasing terkait.',
            'po' => $po,
        ]);
    }
}
