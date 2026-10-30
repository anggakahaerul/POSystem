<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'received_quantity',
        'received_at',
        'unit_price',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'subtotal_after_discount',
        'is_pph',
        'pph_type',
        'pph_percentage',
        'pph_amount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'received_quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal_after_discount' => 'decimal:2',
        'is_pph' => 'boolean',
        'pph_percentage' => 'decimal:2',
        'pph_amount' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    /**
     * Get PPh Label description for detail
     */
    public function getPphLabelAttribute(): string
    {
        if (!$this->is_pph || $this->pph_percentage <= 0) {
            return '-';
        }

        return 'PPh';
    }

    /**
     * Boot method - auto calculate subtotal
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail) {
            $detail->subtotal = $detail->quantity * $detail->unit_price;
        });

        static::updating(function ($detail) {
            $detail->subtotal = $detail->quantity * $detail->unit_price;
        });

        static::saved(function ($detail) {
            // Recalculate PO total after save
            $detail->purchaseOrder->recalculateTotal();
        });

        static::deleted(function ($detail) {
            // Recalculate PO total after delete
            if ($detail->purchaseOrder) {
                $detail->purchaseOrder->recalculateTotal();
            }
        });
    }

    /**
     * Relationship: Detail belongs to purchase order
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Relationship: Detail belongs to product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: Detail has many synced inventory assets (Opsi A)
     */
    public function assets()
    {
        return $this->hasMany(Asset::class, 'purchase_order_detail_id');
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        $amount = $this->subtotal_after_discount ?: $this->subtotal;
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        if ($this->discount_amount > 0) {
            return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
        }
        return '-';
    }

    /**
     * Check if item is fully received
     */
    public function getIsFullyReceivedAttribute(): bool
    {
        return ($this->received_quantity ?? 0) >= $this->quantity;
    }

    /**
     * Check if item is partially received
     */
    public function getIsPartiallyReceivedAttribute(): bool
    {
        $received = $this->received_quantity ?? 0;
        return $received > 0 && $received < $this->quantity;
    }

    /**
     * Get remaining quantity to receive
     */
    public function getRemainingReceiveQuantityAttribute(): int
    {
        return max(0, $this->quantity - ($this->received_quantity ?? 0));
    }

    /**
     * Get receipt status badge
     */
    public function getReceiptStatusBadgeAttribute(): string
    {
        if ($this->is_fully_received) {
            return 'success';
        } elseif ($this->is_partially_received) {
            return 'info';
        }
        return 'secondary';
    }

    /**
     * Get receipt status label
     */
    public function getReceiptStatusLabelAttribute(): string
    {
        if ($this->is_fully_received) {
            return 'Diterima';
        } elseif ($this->is_partially_received) {
            return 'Sebagian (' . ($this->received_quantity ?? 0) . '/' . $this->quantity . ')';
        }
        return 'Belum';
    }
}
