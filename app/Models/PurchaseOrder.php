<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'po_number',
        'vendor_id',
        'branch_id',
        'department_id',
        'payment_term_id',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'ppn_percentage',
        'ppn_amount',
        'is_pph',
        'pph_type',
        'pph_percentage',
        'pph_amount',
        'total_amount',
        'status',
        'tax_review_status',
        'tax_reviewed_by',
        'tax_reviewed_at',
        'tax_notes',
        'tax_reviewer_email',
        'tax_review_requested_at',
        'approval_token',
        'approver_email',
        'approval_token_expires_at',
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
        'revision_count',
        'revision_reason',
        'revised_by',
        'revised_at',
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
        'is_pph' => 'boolean',
        'pph_percentage' => 'decimal:2',
        'pph_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'required_date' => 'date',
        'payment_due_date' => 'date',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'tax_reviewed_at' => 'datetime',
        'tax_review_requested_at' => 'datetime',
        'approval_token_expires_at' => 'datetime',
        'revision_count' => 'integer',
        'revised_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const TAX_REVIEW_PENDING = 'pending';
    const TAX_REVIEW_VERIFIED = 'verified';
    const TAX_REVIEW_REVISED = 'revised';

    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID = 'paid';

    const RECEIPT_NOT_RECEIVED = 'not_received';
    const RECEIPT_PARTIAL = 'partial';
    const RECEIPT_RECEIVED = 'received';

    /**
     * Relationship: PO belongs to purchase request (optional)
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

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
     * Relationship: PO belongs to department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
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
     * Relationship: PO tax reviewed by user
     */
    public function taxReviewer()
    {
        return $this->belongsTo(User::class, 'tax_reviewed_by');
    }

    /**
     * Get tax review status badge class
     */
    public function getTaxReviewBadgeAttribute(): string
    {
        return match ($this->tax_review_status) {
            self::TAX_REVIEW_VERIFIED => 'success',
            self::TAX_REVIEW_REVISED => 'info',
            default => 'warning',
        };
    }

    /**
     * Get tax review status label
     */
    public function getTaxReviewLabelAttribute(): string
    {
        return match ($this->tax_review_status) {
            self::TAX_REVIEW_VERIFIED => 'Terverifikasi',
            self::TAX_REVIEW_REVISED => 'Terkoreksi',
            default => 'Menunggu Review',
        };
    }

    /**
     * Relationship: PO received by user
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Relationship: PO revised by user
     */
    public function revisedBy()
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    /**
     * Relationship: PO has many revision history records
     */
    public function revisions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PurchaseOrderRevision::class)->orderBy('revision_number', 'desc');
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
     * Check if PO can be revised
     * Allowed for PO with status pending (awaiting approval), rejected, or approved
     */
    public function canRevise(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        // Pending, rejected, or approved POs can be pulled for revision
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_REJECTED, self::STATUS_APPROVED])) {
            return false;
        }

        // User must be the creator, an admin, manager, or have edit permission
        return $user->id === $this->created_by
            || $user->hasRole(['Admin', 'Manager', 'General Manager', 'Regional Manager'])
            || $user->can('edit purchase-orders');
    }

    /**
     * Revise PO: resets status back to draft, invalidates previous approval token,
     * resets approval state if previously approved, increments revision counter, and saves revision reason.
     */
    public function revise(string $reason, ?int $userId = null): bool
    {
        if (!$this->canRevise()) {
            return false;
        }

        $previousStatus = $this->status;
        $previousTotal = $this->total_amount;
        $newRevisionCount = (int)($this->revision_count ?? 0) + 1;

        $this->status = self::STATUS_DRAFT;
        $this->revision_count = $newRevisionCount;
        $this->revision_reason = $reason;
        $this->revised_by = $userId ?? auth()->id();
        $this->revised_at = now();

        // Invalidate existing approval magic token
        $this->approval_token = null;
        $this->approval_token_expires_at = null;

        // Reset approval fields so the revised PO goes through the approval process again
        $this->approved_by = null;
        $this->approved_at = null;
        $this->approver_signature = null;

        // If PO has PPh, reset tax review to pending so tax team can re-verify after edit
        if ($this->is_pph) {
            $this->tax_review_status = self::TAX_REVIEW_PENDING;
        }

        $saved = $this->save();

        if ($saved) {
            $this->revisions()->create([
                'revision_number' => $newRevisionCount,
                'reason' => $reason,
                'revised_by' => $this->revised_by,
                'previous_status' => $previousStatus,
                'previous_total_amount' => $previousTotal,
            ]);
        }

        return $saved;
    }

    /**
     * Check if PO is waiting for tax team review (specific to POs with PPh enabled)
     */
    public function isWaitingTaxReview(): bool
    {
        return $this->status === self::STATUS_DRAFT
            && (bool)$this->is_pph
            && $this->tax_review_status === self::TAX_REVIEW_PENDING;
    }

    /**
     * Check if PO tax review has been completed
     */
    public function isTaxReviewed(): bool
    {
        return in_array($this->tax_review_status, [self::TAX_REVIEW_VERIFIED, self::TAX_REVIEW_REVISED]);
    }

    /**
     * Check if PO can be submitted
     */
    public function canSubmit(): bool
    {
        if ($this->status !== self::STATUS_DRAFT || $this->details()->count() === 0) {
            return false;
        }

        // If PPh is enabled, it must be reviewed and verified by tax team first
        if ($this->is_pph && $this->tax_review_status === self::TAX_REVIEW_PENDING) {
            return false;
        }

        return true;
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
     * Get status icon class
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'fas fa-fw fa-pencil-alt',
            self::STATUS_PENDING => 'fas fa-fw fa-clock',
            self::STATUS_APPROVED => 'fas fa-fw fa-check-circle',
            self::STATUS_REJECTED => 'fas fa-fw fa-times-circle',
            default => 'fas fa-fw fa-question-circle',
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
     * Get payment status icon class
     */
    public function getPaymentStatusIconAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID => 'fas fa-fw fa-check-circle',
            self::PAYMENT_PARTIAL => 'fas fa-fw fa-hourglass-half',
            self::PAYMENT_UNPAID => 'fas fa-fw fa-times-circle',
            default => 'fas fa-fw fa-question-circle',
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
     * Get receipt status icon class
     */
    public function getReceiptStatusIconAttribute(): string
    {
        return match ($this->receipt_status) {
            self::RECEIPT_RECEIVED => 'fas fa-fw fa-check-double',
            self::RECEIPT_PARTIAL => 'fas fa-fw fa-box-open',
            self::RECEIPT_NOT_RECEIVED => 'fas fa-fw fa-box',
            default => 'fas fa-fw fa-question-circle',
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
     * Get roadmap stepper details and current position info
     */
    public function getRoadmapAttribute(): array
    {
        // Step 1: PO Dibuat
        $step1 = [
            'step' => 1,
            'name' => 'PO Dibuat',
            'status' => 'completed',
            'badge' => 'Draft Selesai',
            'badge_class' => 'success',
            'icon' => 'fas fa-check-circle',
            'actor' => $this->creator->name ?? 'User',
            'time' => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '-',
        ];

        // Step 2: PO Disubmit
        $isSubmitted = in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]);
        $waitingTax = $this->isWaitingTaxReview();
        $step2Status = $isSubmitted ? 'completed' : ($waitingTax ? 'current status-warning' : 'greyscale');
        $step2Badge = $isSubmitted ? 'Telah Disubmit' : ($waitingTax ? 'Review Tax' : 'Belum Disubmit');
        $step2BadgeClass = $isSubmitted ? 'success' : ($waitingTax ? 'warning' : 'secondary');
        $step2Icon = $isSubmitted ? 'fas fa-check' : ($waitingTax ? 'fas fa-search-dollar' : 'fas fa-hourglass-start');
        $step2Actor = $isSubmitted ? ($this->creator->name ?? 'User') : ($waitingTax ? 'Tim Tax' : '-');

        $step2 = [
            'step' => 2,
            'name' => 'PO Disubmit',
            'status' => $step2Status,
            'badge' => $step2Badge,
            'badge_class' => $step2BadgeClass,
            'icon' => $step2Icon,
            'actor' => $step2Actor,
            'time' => $isSubmitted && $this->created_at ? $this->created_at->format('d/m/Y H:i') : '-',
        ];

        // Step 3: Persetujuan
        $isApproved = ($this->status === self::STATUS_APPROVED);
        $isRejected = ($this->status === self::STATUS_REJECTED);
        $isPending = ($this->status === self::STATUS_PENDING);

        if ($isApproved) {
            $step3Status = 'completed';
            $step3Badge = 'Disetujui';
            $step3BadgeClass = 'success';
            $step3Icon = 'fas fa-check-circle';
            $step3Actor = $this->approver->name ?? 'Approver';
            $step3Time = $this->approved_at ? $this->approved_at->format('d/m/Y H:i') : '-';
        } elseif ($isRejected) {
            $step3Status = 'rejected';
            $step3Badge = 'Ditolak';
            $step3BadgeClass = 'danger';
            $step3Icon = 'fas fa-times-circle';
            $step3Actor = $this->approver->name ?? 'Approver';
            $step3Time = $this->approved_at ? $this->approved_at->format('d/m/Y H:i') : '-';
        } elseif ($isPending) {
            $step3Status = 'current status-warning';
            $step3Badge = 'Menunggu Approval';
            $step3BadgeClass = 'warning';
            $step3Icon = 'fas fa-clock';
            $step3Actor = 'Dalam Antrean';
            $step3Time = '-';
        } else {
            $step3Status = 'greyscale';
            $step3Badge = 'Draft';
            $step3BadgeClass = 'secondary';
            $step3Icon = 'fas fa-minus-circle';
            $step3Actor = '-';
            $step3Time = '-';
        }
        $step3 = [
            'step' => 3,
            'name' => 'Persetujuan',
            'status' => $step3Status,
            'badge' => $step3Badge,
            'badge_class' => $step3BadgeClass,
            'icon' => $step3Icon,
            'actor' => $step3Actor,
            'time' => $step3Time,
        ];

        // Step 4: Pembayaran
        $isPaidFull = ($this->payment_status === self::PAYMENT_PAID);
        $isPaidPartial = ($this->payment_status === self::PAYMENT_PARTIAL);

        if ($isPaidFull) {
            $step4Status = 'completed';
            $step4Badge = 'Lunas';
            $step4BadgeClass = 'success';
            $step4Icon = 'fas fa-check-circle';
        } elseif ($isPaidPartial) {
            $step4Status = 'current status-info';
            $step4Badge = 'Sebagian';
            $step4BadgeClass = 'info';
            $step4Icon = 'fas fa-hourglass-half';
        } elseif ($this->status === self::STATUS_APPROVED) {
            $step4Status = 'current status-pending';
            $step4Badge = 'Belum Lunas';
            $step4BadgeClass = 'danger';
            $step4Icon = 'fas fa-times-circle';
        } else {
            $step4Status = 'greyscale';
            $step4Badge = 'Belum Lunas';
            $step4BadgeClass = 'secondary';
            $step4Icon = 'fas fa-times-circle';
        }
        $step4 = [
            'step' => 4,
            'name' => 'Pembayaran',
            'status' => $step4Status,
            'badge' => $step4Badge,
            'badge_class' => $step4BadgeClass,
            'icon' => $step4Icon,
        ];

        // Step 5: Penerimaan Barang
        $totalQtyOrdered = $this->details ? $this->details->sum('quantity') : 0;
        $totalQtyReceived = $this->details ? $this->details->sum('received_quantity') : 0;
        $receiptPercent = $totalQtyOrdered > 0 ? round(($totalQtyReceived / $totalQtyOrdered) * 100) : 0;

        $isReceivedFull = ($this->status === self::STATUS_APPROVED && $this->receipt_status === self::RECEIPT_RECEIVED);
        $isReceivedPartial = ($this->status === self::STATUS_APPROVED && $this->receipt_status === self::RECEIPT_PARTIAL);
        $isWaitingReceipt = ($this->status === self::STATUS_APPROVED && $this->receipt_status === self::RECEIPT_NOT_RECEIVED);

        if ($isReceivedFull) {
            $step5Status = 'completed';
            $step5Badge = 'Diterima Lengkap';
            $step5BadgeClass = 'success';
            $step5Icon = 'fas fa-check-circle';
        } elseif ($isReceivedPartial) {
            $step5Status = 'current status-info';
            $step5Badge = 'Sebagian (' . $totalQtyReceived . '/' . $totalQtyOrdered . ')';
            $step5BadgeClass = 'info';
            $step5Icon = 'fas fa-boxes';
        } elseif ($isWaitingReceipt) {
            $step5Status = 'current status-pending';
            $step5Badge = 'Belum Diterima';
            $step5BadgeClass = 'secondary';
            $step5Icon = 'fas fa-box';
        } else {
            $step5Status = 'greyscale';
            $step5Badge = 'Belum Diterima';
            $step5BadgeClass = 'secondary';
            $step5Icon = 'fas fa-box';
        }
        $step5 = [
            'step' => 5,
            'name' => 'Penerimaan Barang',
            'status' => $step5Status,
            'badge' => $step5Badge,
            'badge_class' => $step5BadgeClass,
            'icon' => $step5Icon,
            'percent' => $receiptPercent,
            'received' => $totalQtyReceived,
            'ordered' => $totalQtyOrdered,
        ];

        // Step 6: PO Selesai
        $isFinished = ($this->status === self::STATUS_APPROVED && $this->receipt_status === self::RECEIPT_RECEIVED && $this->payment_status === self::PAYMENT_PAID);
        $step6 = [
            'step' => 6,
            'name' => 'PO Selesai',
            'status' => $isFinished ? 'completed' : 'greyscale',
            'badge' => $isFinished ? 'Tuntas Penuh' : 'Dalam Proses',
            'badge_class' => $isFinished ? 'success' : 'secondary',
            'icon' => $isFinished ? 'fas fa-flag-checkered' : 'fas fa-hourglass-half',
        ];

        // Determine current stage info
        if ($isFinished) {
            $currentStep = 6;
            $currentTitle = 'PO Selesai';
            $currentLabel = 'Langkah 6/6: Tuntas Penuh';
            $currentBadgeClass = 'success';
            $progress = 100;
        } elseif ($isRejected) {
            $currentStep = 3;
            $currentTitle = 'Persetujuan';
            $currentLabel = 'Langkah 3/6: Ditolak';
            $currentBadgeClass = 'danger';
            $progress = 45;
        } elseif ($isPending) {
            $currentStep = 3;
            $currentTitle = 'Persetujuan';
            $currentLabel = 'Langkah 3/6: Menunggu Approval';
            $currentBadgeClass = 'warning';
            $progress = 35;
        } elseif ($this->status === self::STATUS_DRAFT) {
            if ($this->isWaitingTaxReview()) {
                $currentStep = 2;
                $currentTitle = 'Review Tax';
                $currentLabel = 'Langkah 2/6: Menunggu Review Tax';
                $currentBadgeClass = 'warning';
                $progress = 25;
            } else {
                $currentStep = 1;
                $currentTitle = 'PO Dibuat';
                $currentLabel = 'Langkah 1/6: Draft';
                $currentBadgeClass = 'secondary';
                $progress = 15;
            }
        } elseif ($isApproved) {
            if ($isReceivedFull && $isPaidFull) {
                $currentStep = 6;
                $currentTitle = 'PO Selesai';
                $currentLabel = 'Langkah 6/6: Selesai';
                $currentBadgeClass = 'success';
                $progress = 100;
            } elseif ($isPaidFull) {
                $currentStep = 5;
                $currentTitle = 'Penerimaan Barang';
                $currentLabel = 'Langkah 5/6: Barang (' . $this->receipt_status_label . ')';
                $currentBadgeClass = 'info';
                $progress = 85;
            } elseif ($isReceivedFull) {
                $currentStep = 4;
                $currentTitle = 'Pembayaran';
                $currentLabel = 'Langkah 4/6: Pembayaran (' . $this->payment_status_label . ')';
                $currentBadgeClass = 'info';
                $progress = 85;
            } elseif ($isPaidPartial || $isReceivedPartial) {
                $currentStep = 4;
                $currentTitle = 'Pembayaran / Penerimaan';
                $currentLabel = 'Langkah 4 & 5: Proses Sebagian';
                $currentBadgeClass = 'info';
                $progress = 65;
            } else {
                $currentStep = 4;
                $currentTitle = 'Pembayaran';
                $currentLabel = 'Langkah 4/6: Disetujui (Siap Dibayar)';
                $currentBadgeClass = 'primary';
                $progress = 50;
            }
        } else {
            $currentStep = 1;
            $currentTitle = 'Draft';
            $currentLabel = 'Langkah 1/6: Draft';
            $currentBadgeClass = 'secondary';
            $progress = 15;
        }

        return [
            'steps' => [$step1, $step2, $step3, $step4, $step5, $step6],
            'current_step' => $currentStep,
            'current_title' => $currentTitle,
            'current_label' => $currentLabel,
            'current_badge_class' => $currentBadgeClass,
            'progress' => $progress,
        ];
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
    public function approve(?int $approverId = null): bool
    {
        if (!$this->canApprove()) {
            return false;
        }

        $this->status = self::STATUS_APPROVED;
        if ($approverId) {
            $this->approved_by = $approverId;
        }
        $this->approved_at = now();
        $this->approval_token = null; // Invalidate token once processed

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
    public function reject(?int $approverId = null, string $reason = ''): bool
    {
        if (!$this->canApprove()) {
            return false;
        }

        $this->status = self::STATUS_REJECTED;
        if ($approverId) {
            $this->approved_by = $approverId;
        }
        $this->rejection_reason = $reason;
        $this->approved_at = now();
        $this->approval_token = null; // Invalidate token once processed
        return $this->save();
    }

    /**
     * Check if a given approval token is valid
     */
    public function isApprovalTokenValid(string $token): bool
    {
        if (empty($this->approval_token) || $this->approval_token !== $token) {
            return false;
        }

        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        if ($this->approval_token_expires_at && now()->greaterThan($this->approval_token_expires_at)) {
            return false;
        }

        return true;
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
                $previousReceivedQty = (int) $detail->received_quantity;
                // Add to existing received quantity (allow multiple receptions)
                $newReceivedQty = $previousReceivedQty + $receivedQty;
                // Cap at ordered quantity
                $newReceivedQty = min($newReceivedQty, $detail->quantity);
                $deltaQty = max(0, $newReceivedQty - $previousReceivedQty);

                $detail->received_quantity = $newReceivedQty;
                $detail->received_at = now();
                $detail->save();

                if ($deltaQty > 0) {
                    Asset::syncFromPurchaseOrderDetail($detail, $deltaQty, $receiverId);
                }
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
            $previousReceivedQty = (int) $detail->received_quantity;
            $deltaQty = max(0, (int) $detail->quantity - $previousReceivedQty);

            $detail->received_quantity = $detail->quantity;
            $detail->received_at = now();
            $detail->save();

            if ($deltaQty > 0) {
                Asset::syncFromPurchaseOrderDetail($detail, $deltaQty, $receiverId);
            }
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

        // Calculate PPh (Withholding tax: reduces total payable)
        // If items have individual PPh, sum item-level PPh amounts
        $itemPphTotal = (float) $this->details()->where('is_pph', true)->sum('pph_amount');

        if ($itemPphTotal > 0) {
            $this->pph_amount = $itemPphTotal;
        } elseif ($this->is_pph) {
            if (in_array($this->pph_type, ['custom_nominal', 'nominal'])) {
                // If custom nominal, use existing pph_amount or fallback from percentage
                if ((float) $this->pph_amount <= 0 && $this->pph_percentage > 0) {
                    $this->pph_amount = ($afterDiscount * $this->pph_percentage) / 100;
                }
                if ($afterDiscount > 0 && (float) $this->pph_amount > 0) {
                    $this->pph_percentage = round(($this->pph_amount / $afterDiscount) * 100, 4);
                }
            } elseif ($this->pph_percentage > 0) {
                // Otherwise use document-level PPh percentage
                $this->pph_amount = ($afterDiscount * $this->pph_percentage) / 100;
            } else {
                $this->pph_amount = 0;
            }
        } else {
            $this->pph_amount = 0;
        }

        // Final total (Subtotal - Discount + PPN - PPh)
        $this->total_amount = $afterDiscount + $this->ppn_amount - $this->pph_amount;
        $this->save();
    }

    /**
     * Get PPh Label description
     */
    public function getPphLabelAttribute(): string
    {
        return 'PPh';
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
