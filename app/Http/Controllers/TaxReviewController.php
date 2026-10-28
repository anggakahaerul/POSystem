<?php

namespace App\Http\Controllers;

use App\Mail\POTaxReviewCompleted;
use App\Mail\POTaxReviewRequest;
use App\Models\PurchaseOrder;
use App\Models\Setting;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaxReviewController extends Controller
{
    /**
     * Display list of POs for tax review.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('view tax') && !auth()->user()->can('review tax')) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat menu Review Pajak.');
        }

        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = PurchaseOrder::with(['vendor', 'taxReviewer', 'details'])
            ->where('is_pph', true)
            ->latest();

        if ($status && $status !== 'all') {
            $query->where('tax_review_status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($vq) use ($search) {
                      $vq->where('name', 'like', "%{$search}%")
                         ->orWhere('npwp', 'like', "%{$search}%");
                  });
            });
        }

        $purchaseOrders = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => PurchaseOrder::where('is_pph', true)->count(),
            'pending' => PurchaseOrder::where('is_pph', true)->where('tax_review_status', 'pending')->count(),
            'verified' => PurchaseOrder::where('is_pph', true)->where('tax_review_status', 'verified')->count(),
            'revised' => PurchaseOrder::where('is_pph', true)->where('tax_review_status', 'revised')->count(),
        ];

        return view('tax-review.index', compact('purchaseOrders', 'stats', 'status', 'search'));
    }

    /**
     * Show PO details for tax review modal (JSON).
     */
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!auth()->user()->can('view tax') && !auth()->user()->can('review tax')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk melihat detail review pajak.',
            ], 403);
        }

        $purchaseOrder->load(['vendor', 'details.product', 'taxReviewer']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'created_at' => $purchaseOrder->created_at->format('d/m/Y'),
                'status' => $purchaseOrder->status,
                'status_label' => $purchaseOrder->status_label,
                'tax_review_status' => $purchaseOrder->tax_review_status,
                'tax_review_label' => $purchaseOrder->tax_review_label,
                'tax_review_badge' => $purchaseOrder->tax_review_badge,
                'tax_notes' => $purchaseOrder->tax_notes,
                'tax_reviewer_email' => $purchaseOrder->tax_reviewer_email,
                'tax_review_requested_at' => $purchaseOrder->tax_review_requested_at?->format('d/m/Y H:i'),
                'tax_reviewed_by' => $purchaseOrder->taxReviewer?->name,
                'tax_reviewed_at' => $purchaseOrder->tax_reviewed_at?->format('d/m/Y H:i'),
                'vendor' => [
                    'id' => $purchaseOrder->vendor_id,
                    'name' => $purchaseOrder->vendor?->name ?? '-',
                    'npwp' => $purchaseOrder->vendor?->npwp ?? '',
                    'npwp_type' => $purchaseOrder->vendor?->npwp_type ?? '',
                ],
                'subtotal' => (float)$purchaseOrder->subtotal,
                'discount_amount' => (float)$purchaseOrder->discount_amount,
                'ppn_percentage' => (float)$purchaseOrder->ppn_percentage,
                'ppn_amount' => (float)$purchaseOrder->ppn_amount,
                'is_pph' => (bool)$purchaseOrder->is_pph,
                'pph_type' => $purchaseOrder->pph_type ?? 'pph23',
                'pph_percentage' => (float)$purchaseOrder->pph_percentage,
                'pph_amount' => (float)$purchaseOrder->pph_amount,
                'total_amount' => (float)$purchaseOrder->total_amount,
                'items' => $purchaseOrder->details->map(function ($d) {
                    return [
                        'id' => $d->id,
                        'product_name' => $d->product?->name ?? 'Produk',
                        'quantity' => $d->quantity,
                        'unit_price' => (float)$d->unit_price,
                        'subtotal' => (float)($d->subtotal_after_discount > 0 ? $d->subtotal_after_discount : $d->subtotal),
                        'is_pph' => (bool)$d->is_pph,
                        'pph_type' => $d->pph_type ?? 'pph23',
                        'pph_percentage' => (float)$d->pph_percentage,
                        'pph_amount' => (float)$d->pph_amount,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Update tax details and review status.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        if (!auth()->user()->can('review tax')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk melakukan koreksi atau verifikasi pajak.',
            ], 403);
        }

        $validated = $request->validate([
            'npwp' => 'nullable|string|max:30',
            'npwp_type' => 'nullable|string|in:Orang Pribadi,Badan,Instansi Pemerintah',
            'ppn_percentage' => 'nullable|numeric|min:0|max:100',
            'is_pph' => 'nullable|boolean',
            'pph_type' => 'nullable|string|max:50',
            'pph_percentage' => 'nullable|numeric|min:0|max:100',
            'pph_amount' => 'nullable|numeric|min:0',
            'action_type' => 'required|in:verify,revise',
            'tax_notes' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.is_pph' => 'nullable|boolean',
            'items.*.pph_type' => 'nullable|string|max:50',
            'items.*.pph_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.pph_amount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Vendor NPWP and NPWP Type
            if ($purchaseOrder->vendor) {
                $purchaseOrder->vendor->update([
                    'npwp' => $request->input('npwp'),
                    'npwp_type' => $request->input('npwp_type'),
                ]);
            }

            // 2. Update Item-Level PPh (if provided)
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemId => $itemData) {
                    $detail = $purchaseOrder->details()->find($itemId);
                    if ($detail) {
                        $itemIsPph = !empty($itemData['is_pph']);
                        $itemPphType = $itemIsPph ? ($itemData['pph_type'] ?? null) : null;
                        $itemPphPct = $itemIsPph ? (float)($itemData['pph_percentage'] ?? 0) : 0;
                        $baseAmount = $detail->subtotal_after_discount > 0 ? $detail->subtotal_after_discount : $detail->subtotal;
                        
                        if ($itemIsPph && in_array($itemPphType, ['custom_nominal', 'nominal'])) {
                            $itemPphAmt = (float)($itemData['pph_amount'] ?? 0);
                            if ($baseAmount > 0 && $itemPphPct <= 0 && $itemPphAmt > 0) {
                                $itemPphPct = round(($itemPphAmt / $baseAmount) * 100, 4);
                            }
                        } else {
                            $itemPphAmt = $itemIsPph ? ($baseAmount * $itemPphPct / 100) : 0;
                        }

                        $detail->update([
                            'is_pph' => $itemIsPph,
                            'pph_type' => $itemPphType,
                            'pph_percentage' => $itemPphPct,
                            'pph_amount' => $itemPphAmt,
                        ]);
                    }
                }
            }

            // 3. Update PO Level Taxes & Review Status
            $isPph = $request->boolean('is_pph');
            $pphType = $isPph ? $request->input('pph_type') : null;
            $pphPct = $isPph ? (float)$request->input('pph_percentage', 0) : 0;
            $pphAmt = $isPph ? (float)$request->input('pph_amount', 0) : 0;
            $ppnPct = (float)$request->input('ppn_percentage', $purchaseOrder->ppn_percentage);

            $status = $request->action_type === 'verify' 
                ? PurchaseOrder::TAX_REVIEW_VERIFIED 
                : PurchaseOrder::TAX_REVIEW_REVISED;

            $updateData = [
                'ppn_percentage' => $ppnPct,
                'is_pph' => $isPph,
                'pph_type' => $pphType,
                'pph_percentage' => $pphPct,
                'tax_review_status' => $status,
                'tax_reviewed_by' => auth()->id(),
                'tax_reviewed_at' => now(),
                'tax_notes' => $request->input('tax_notes'),
            ];

            if ($isPph && in_array($pphType, ['custom_nominal', 'nominal'])) {
                $updateData['pph_amount'] = $pphAmt;
                $afterDiscount = (float)($purchaseOrder->subtotal - $purchaseOrder->discount_amount);
                if ($afterDiscount > 0 && $pphPct <= 0 && $pphAmt > 0) {
                    $updateData['pph_percentage'] = round(($pphAmt / $afterDiscount) * 100, 4);
                }
            }

            $purchaseOrder->update($updateData);

            // 4. Recalculate totals
            $purchaseOrder->recalculateTotal();

            DB::commit();

            // 5. Send notification email to creator if enabled
            if (Setting::isEmailNotificationEnabled('tax_review')) {
                try {
                    Setting::applyEmailConfig();
                    $purchaseOrder->load('creator');
                    if ($purchaseOrder->creator && !empty($purchaseOrder->creator->email)) {
                        Mail::to($purchaseOrder->creator->email)
                            ->send(new POTaxReviewCompleted($purchaseOrder, $request->action_type));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send tax review completed email: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => $status === PurchaseOrder::TAX_REVIEW_VERIFIED 
                    ? 'Pajak PO berhasil diverifikasi.' 
                    : 'Pajak PO berhasil dikoreksi & disimpan.',
                'data' => [
                    'tax_review_status' => $purchaseOrder->tax_review_status,
                    'tax_review_label' => $purchaseOrder->tax_review_label,
                    'tax_review_badge' => $purchaseOrder->tax_review_badge,
                    'total_amount' => $purchaseOrder->total_amount,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan review pajak: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send tax review request email to tax team / specific reviewer.
     */
    public function sendTaxReviewRequest(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->isWaitingTaxReview()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PO ini tidak sedang dalam status menunggu review pajak.',
                ], 422);
            }
            return back()->with('error', 'PO ini tidak sedang dalam status menunggu review pajak.');
        }

        $validated = $request->validate([
            'reviewer_email' => 'required|email',
            'notes' => 'nullable|string|max:500',
        ], [
            'reviewer_email.required' => 'Alamat email reviewer pajak wajib dipilih atau diisi.',
            'reviewer_email.email' => 'Format alamat email tidak valid.',
        ]);

        try {
            Setting::applyEmailConfig();

            $purchaseOrder->load(['vendor', 'details.product', 'creator', 'paymentTerm', 'branch']);

            $reviewerEmail = $validated['reviewer_email'];
            $notes = $validated['notes'] ?? null;
            $sender = auth()->user();

            Mail::to($reviewerEmail)->send(new POTaxReviewRequest($purchaseOrder, $notes, $sender));

            // Update PO tracking
            $purchaseOrder->update([
                'tax_reviewer_email' => $reviewerEmail,
                'tax_review_requested_at' => now(),
            ]);

            $msg = 'Permohonan review pajak berhasil dikirimkan via email ke ' . $reviewerEmail . '.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'data' => [
                        'tax_reviewer_email' => $reviewerEmail,
                        'tax_review_requested_at' => $purchaseOrder->tax_review_requested_at->format('d/m/Y H:i'),
                    ],
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('Failed to send tax review request email: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim email permohonan review pajak: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal mengirim email permohonan review pajak: ' . $e->getMessage());
        }
    }
}
