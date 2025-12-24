<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class POPayment extends Model
{
    protected $table = 'po_payments';

    protected $fillable = [
        'purchase_order_id',
        'payment_number',
        'description',
        'percentage',
        'amount',
        'due_date',
        'paid_amount',
        'paid_at',
        'status',
        'note',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    /**
     * Relationship: Payment belongs to PO
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get formatted paid amount
     */
    public function getFormattedPaidAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->paid_amount, 0, ',', '.');
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }

    /**
     * Check if fully paid
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid' || $this->paid_amount >= $this->amount;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'paid' => 'success',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'paid' => 'Lunas',
            'pending' => 'Belum Lunas',
            default => $this->status,
        };
    }

    /**
     * Record payment
     */
    public function recordPayment(float $amount, ?string $note = null): void
    {
        $this->paid_amount += $amount;
        $this->paid_at = now();
        $this->note = $note;

        if ($this->paid_amount >= $this->amount) {
            $this->status = 'paid';
        }

        $this->save();

        // Update PO payment status
        $this->purchaseOrder->updatePaymentStatus();
    }
}
