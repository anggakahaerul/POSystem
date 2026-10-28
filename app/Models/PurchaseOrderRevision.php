<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'revision_number',
        'reason',
        'revised_by',
        'previous_status',
        'previous_total_amount',
    ];

    protected $casts = [
        'revision_number' => 'integer',
        'previous_total_amount' => 'decimal:2',
    ];

    /**
     * Relationship: Revision belongs to a Purchase Order
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Relationship: Revision created by a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    /**
     * Alias for user
     */
    public function revisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    /**
     * Formatted previous total amount
     */
    public function getFormattedPreviousTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->previous_total_amount ?? 0, 0, ',', '.');
    }

    /**
     * Get human-readable previous status label
     */
    public function getPreviousStatusLabelAttribute(): string
    {
        return match ($this->previous_status) {
            'approved' => 'Approved (Disetujui)',
            'pending' => 'Pending (Menunggu Approval)',
            'rejected' => 'Rejected (Ditolak)',
            'draft' => 'Draft',
            default => ucfirst($this->previous_status ?? 'Sebelumnya'),
        };
    }
}
