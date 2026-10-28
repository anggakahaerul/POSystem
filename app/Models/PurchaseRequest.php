<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_number',
        'title',
        'description',
        'attachment',
        'attachment_name',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generate automatic PR Number (e.g., PR-202610-0001)
     */
    public static function generatePrNumber(): string
    {
        $prefix = 'PR-' . date('Ym') . '-';
        
        $lastPr = self::where('pr_number', 'like', $prefix . '%')
            ->orderBy('pr_number', 'desc')
            ->first();

        if ($lastPr) {
            $lastNumber = (int) substr($lastPr->pr_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * User who created the request
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Linked Purchase Order (if converted)
     */
    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class, 'purchase_request_id')->latestOfMany();
    }

    /**
     * Linked Purchase Orders (multiple POs per PR)
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'purchase_request_id')->latest();
    }

    /**
     * URL of the attachment file
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment);
    }

    /**
     * Check if attachment is a PDF file
     */
    public function getIsPdfAttribute(): bool
    {
        if (!$this->attachment) {
            return false;
        }

        return strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION)) === 'pdf';
    }

    /**
     * Check if attachment is an image
     */
    public function getIsImageAttribute(): bool
    {
        if (!$this->attachment) {
            return false;
        }

        $ext = strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION));
        return in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
    }

    /**
     * Human-friendly status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'processed' => 'Sudah Jadi PO',
            default => 'Menunggu PO',
        };
    }

    /**
     * Status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'processed' => 'success',
            default => 'warning',
        };
    }
}
