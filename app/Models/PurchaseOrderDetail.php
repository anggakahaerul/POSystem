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
        'unit_price',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'subtotal_after_discount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal_after_discount' => 'decimal:2',
    ];

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
}
