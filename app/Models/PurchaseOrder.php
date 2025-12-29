<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'vendor_id',
        'branch_id',
        'payment_term_id',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'ppn_percentage',
        'ppn_amount',
        'total_amount',
        'status',
        'payment_status',
        'receipt_status',
        'payment_due_date',
        'created_by',
        'creator_signature',
        'approved_by',
        'approver_signature',
        'received_by',
        'note',
        'rejection_reason',
        'required_date',
        'approved_at',
        'received_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'ppn_percentage' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'required_date' => 'date',
        'payment_due_date' => 'date',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID = 'paid';

    const RECEIPT_NOT_RECEIVED = 'not_received';
    const RECEIPT_PARTIAL = 'partial';
    const RECEIPT_RECEIVED = 'received';

    /**
     * Relationship: PO belongs to vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Relationship: PO belongs to branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relationship: PO belongs to payment term
     */
    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    /**
     * Relationship: PO has many details
     */
    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    /**
     * Relationship: PO has many payments (installments)
     */
    public function payments()
    {
        return $this->hasMany(POPayment::class)->orderBy('payment_number');
    }

    /**
     * Relationship: PO created by user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: PO approved by user
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship: PO received by user
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pending approval
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Draft POs
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Check if PO can be edited
     */
    public function canEdit(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if PO can be submitted
     */
    public function canSubmit(): bool
    {
        return $this->status === self::STATUS_DRAFT && $this->details()->count() > 0;
    }

    /**
     * Check if PO can be approved/rejected
     */
    public function canApprove(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Menunggu Approval',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Unknown',
        };
    }

    /**
     * Get payment status badge
     */
    public function getPaymentStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'success',
            self::PAYMENT_PARTIAL => 'info',
            self::PAYMENT_UNPAID => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'Lunas',
            self::PAYMENT_PARTIAL => 'Sebagian',
            self::PAYMENT_UNPAID => 'Belum Lunas',
            default => 'Unknown',
        };
    }

    /**
     * Get receipt status badge
     */
    public function getReceiptStatusBadgeAttribute(): string
    {
        return match ($this->receipt_status) {
            self::RECEIPT_RECEIVED => 'success',
            self::RECEIPT_PARTIAL => 'info',
            self::RECEIPT_NOT_RECEIVED => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get receipt status label
     */
    public function getReceiptStatusLabelAttribute(): string
    {
        return match ($this->receipt_status) {
            self::RECEIPT_RECEIVED => 'Diterima',
            self::RECEIPT_PARTIAL => 'Sebagian',
            self::RECEIPT_NOT_RECEIVED => 'Belum Diterima',
            default => 'Unknown',
        };
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Submit PO for approval
     */
    public function submit(): bool
    {
        if (!$this->canSubmit()) {
            return false;
        }

        $this->status = self::STATUS_PENDING;
        return $this->save();
    }

    /**
     * Approve PO and auto-generate payment schedules
     */
    public function approve(int $approverId): bool
    {
        if (!$this->canApprove()) {
            return false;
        }

        $this->status = self::STATUS_APPROVED;
        $this->approved_by = $approverId;
        $this->approved_at = now();

        // Calculate payment due date if payment term exists
        if ($this->paymentTerm) {
            $this->payment_due_date = now()->addDays($this->paymentTerm->days);
        }

        $saved = $this->save();

        // Auto-generate payment schedules if payment term exists
        if ($saved && $this->paymentTerm) {
            $this->generatePaymentSchedules();
        }

        return $saved;
    }

    /**
     * Generate payment schedules from payment term
     */
    public function generatePaymentSchedules(): void
    {
        // Clear existing schedules
        $this->payments()->delete();

        if (!$this->paymentTerm) {
            return;
        }

        $schedules = $this->paymentTerm->generateSchedules($this);

        foreach ($schedules as $schedule) {
            POPayment::create([
                'purchase_order_id' => $this->id,
                'payment_number' => $schedule['payment_number'],
                'description' => $schedule['description'],
                'percentage' => $schedule['percentage'],
                'amount' => $schedule['amount'],
                'due_date' => $schedule['due_date'],
                'status' => $schedule['status'],
            ]);
        }

        $this->updatePaymentStatus();
    }

    /**
     * Reject PO
     */
    public function reject(int $approverId, string $reason): bool
    {
        if (!$this->canApprove()) {
            return false;
        }

        $this->status = self::STATUS_REJECTED;
        $this->approved_by = $approverId;
        $this->rejection_reason = $reason;
        $this->approved_at = now();
        return $this->save();
    }

    /**
     * Check if goods can be received
     * Requires: approved status only (flexible - before or after payment)
     */
    public function canReceive(): bool
    {
        // Must be approved
        if ($this->status !== self::STATUS_APPROVED) {
            return false;
        }

        // Already fully received
        if ($this->receipt_status === self::RECEIPT_RECEIVED) {
            return false;
        }

        return true;
    }

    /**
     * Update payment status based on payment schedules
     */
    public function updatePaymentStatus(): void
    {
        $totalPayments = $this->payments()->sum('amount');
        $totalPaid = $this->payments()->sum('paid_amount');

        if ($totalPayments == 0) {
            // No payment schedule, keep current status
            return;
        }

        if ($totalPaid >= $totalPayments) {
            $this->payment_status = self::PAYMENT_PAID;
        } elseif ($totalPaid > 0) {
            $this->payment_status = self::PAYMENT_PARTIAL;
        } else {
            $this->payment_status = self::PAYMENT_UNPAID;
        }

        $this->save();
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('paid_amount') ?? 0;
    }

    /**
     * Get remaining amount to pay
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->total_paid;
    }

    /**
     * Receive items with specific quantities
     * @param array $items Array of [detail_id => received_quantity]
     * @param int $receiverId User ID who received
     * @return bool
     */
    public function receiveItems(array $items, int $receiverId): bool
    {
        if (!$this->canReceive()) {
            return false;
        }

        foreach ($items as $detailId => $receivedQty) {
            $detail = $this->details()->find($detailId);
            if ($detail && $receivedQty > 0) {
                // Add to existing received quantity (allow multiple receptions)
                $newReceivedQty = $detail->received_quantity + $receivedQty;
                // Cap at ordered quantity
                $newReceivedQty = min($newReceivedQty, $detail->quantity);
                
                $detail->received_quantity = $newReceivedQty;
                $detail->received_at = now();
                $detail->save();
            }
        }

        $this->received_by = $receiverId;
        $this->received_at = now();
        $this->updateReceiptStatus();

        return true;
    }

    /**
     * Update receipt status based on received quantities
     */
    public function updateReceiptStatus(): void
    {
        $totalQtyOrdered = $this->details()->sum('quantity');
        $totalQtyReceived = $this->details()->sum('received_quantity');

        if ($totalQtyReceived >= $totalQtyOrdered) {
            $this->receipt_status = self::RECEIPT_RECEIVED;
        } elseif ($totalQtyReceived > 0) {
            $this->receipt_status = self::RECEIPT_PARTIAL;
        } else {
            $this->receipt_status = self::RECEIPT_NOT_RECEIVED;
        }

        $this->save();
    }

    /**
     * Receive all goods (full) - for backward compatibility
     */
    public function receiveGoods(int $receiverId): bool
    {
        if (!$this->canReceive()) {
            return false;
        }

        // Mark all items as fully received
        foreach ($this->details as $detail) {
            $detail->received_quantity = $detail->quantity;
            $detail->received_at = now();
            $detail->save();
        }

        $this->receipt_status = self::RECEIPT_RECEIVED;
        $this->received_by = $receiverId;
        $this->received_at = now();
        return $this->save();
    }

    /**
     * Partial receive goods - for backward compatibility
     */
    public function partialReceive(int $receiverId): bool
    {
        if (!$this->canReceive()) {
            return false;
        }

        $this->receipt_status = self::RECEIPT_PARTIAL;
        $this->received_by = $receiverId;
        $this->received_at = now();
        return $this->save();
    }

    /**
     * Create payment schedule from percentages
     */
    public function createPaymentSchedule(array $termins): void
    {
        // Delete existing payments
        $this->payments()->delete();

        $paymentNumber = 1;
        foreach ($termins as $termin) {
            $amount = ($termin['percentage'] / 100) * $this->total_amount;
            $dueDate = $this->payment_due_date;

            // Adjust due date for each termin if needed
            if (isset($termin['days_after'])) {
                $dueDate = $this->approved_at 
                    ? $this->approved_at->copy()->addDays($termin['days_after'])
                    : now()->addDays($termin['days_after']);
            }

            POPayment::create([
                'purchase_order_id' => $this->id,
                'payment_number' => $paymentNumber,
                'description' => $termin['description'],
                'percentage' => $termin['percentage'],
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);

            $paymentNumber++;
        }
    }

    /**
     * Recalculate total amount from details with discount and PPN
     */
    public function recalculateTotal(): void
    {
        // Calculate subtotal from details (after item discount)
        $subtotal = $this->details()->sum('subtotal_after_discount');
        
        // Fallback to subtotal if no after_discount column yet
        if ($subtotal == 0) {
            $subtotal = $this->details()->sum('subtotal');
        }

        $this->subtotal = $subtotal;

        // Calculate document discount
        if ($this->discount_type === 'percentage' && $this->discount_value > 0) {
            $this->discount_amount = ($subtotal * $this->discount_value) / 100;
        } elseif ($this->discount_type === 'fixed' && $this->discount_value > 0) {
            $this->discount_amount = $this->discount_value;
        } else {
            $this->discount_amount = 0;
        }

        // After document discount
        $afterDiscount = $subtotal - $this->discount_amount;

        // Calculate PPN
        if ($this->ppn_percentage > 0) {
            $this->ppn_amount = ($afterDiscount * $this->ppn_percentage) / 100;
        } else {
            $this->ppn_amount = 0;
        }

        // Final total
        $this->total_amount = $afterDiscount + $this->ppn_amount;
        $this->save();
    }

    /**
     * Generate unique PO number with branch code
     * Format: XXX/GA/BRANCH_CODE/ROMAN_MONTH/YY
     * Example: 009/GA/PLJKT/I/25
     */
    public static function generatePoNumber(?int $branchId = null): string
    {
        $month = (int) date('n');
        $year = date('y');
        $romanMonth = Branch::monthToRoman($month);

        // Get branch code
        $branchCode = 'MAIN'; // Default if no branch
        if ($branchId) {
            $branch = Branch::find($branchId);
            if ($branch) {
                $branchCode = $branch->code;
            }
        }

        // Pattern to search: /GA/{branchCode}/{romanMonth}/{year}
        $suffix = "/GA/{$branchCode}/{$romanMonth}/{$year}";

        // Find last PO with this pattern
        $lastPO = self::where('branch_id', $branchId)
            ->where('po_number', 'like', '%' . $suffix)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', date('Y'))
            ->orderByRaw('CAST(SUBSTRING_INDEX(po_number, "/", 1) AS UNSIGNED) DESC')
            ->first();

        if ($lastPO) {
            $lastNumber = intval(explode('/', $lastPO->po_number)[0]);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $sequence = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        return "{$sequence}{$suffix}";
    }

    /**
     * Create PO with details (using transaction)
     */
    public static function createWithDetails(array $poData, array $details): self
    {
        return DB::transaction(function () use ($poData, $details) {
            $po = self::create($poData);

            foreach ($details as $detail) {
                $detail['purchase_order_id'] = $po->id;
                PurchaseOrderDetail::create($detail);
            }

            $po->recalculateTotal();

            return $po;
        });
    }
}
