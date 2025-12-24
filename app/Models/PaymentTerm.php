<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentTerm extends Model
{
    protected $fillable = [
        'name',
        'days',
        'payment_type',
        'installments',
        'description',
        'is_active',
    ];

    protected $casts = [
        'days' => 'integer',
        'is_active' => 'boolean',
        'installments' => 'array',
    ];

    const TYPE_FULL = 'full';
    const TYPE_PARTIAL = 'partial';

    /**
     * Scope: Active payment terms
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relationship: Payment term has many POs
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->days == 0 && $this->payment_type === self::TYPE_FULL) {
            return $this->name;
        }

        if ($this->payment_type === self::TYPE_PARTIAL) {
            return "{$this->name} (Cicilan)";
        }

        return "{$this->name} ({$this->days} hari)";
    }

    /**
     * Check if this is a partial payment term
     */
    public function isPartial(): bool
    {
        return $this->payment_type === self::TYPE_PARTIAL;
    }

    /**
     * Get installments config or default to 100%
     */
    public function getInstallmentsConfig(): array
    {
        if ($this->payment_type === self::TYPE_PARTIAL && !empty($this->installments)) {
            $installments = $this->installments;
            
            // Handle case where installments is stored as string (double-encoded JSON)
            if (is_string($installments)) {
                $installments = json_decode($installments, true);
            }
            
            if (is_array($installments)) {
                return $installments;
            }
        }

        // Full payment - single 100% installment
        return [
            ['percentage' => 100, 'days' => $this->days, 'description' => 'Pembayaran Penuh']
        ];
    }

    /**
     * Generate payment schedules for a PO
     */
    public function generateSchedules(PurchaseOrder $po): array
    {
        $schedules = [];
        $installments = $this->getInstallmentsConfig();
        $baseDate = $po->approved_at ?? now();

        foreach ($installments as $index => $installment) {
            $percentage = $installment['percentage'] ?? 0;
            $days = $installment['days'] ?? 0;
            $description = $installment['description'] ?? "Termin " . ($index + 1);

            $schedules[] = [
                'payment_number' => $index + 1,
                'description' => $description,
                'percentage' => $percentage,
                'amount' => ($po->total_amount * $percentage) / 100,
                'due_date' => $baseDate->copy()->addDays($days),
                'status' => 'pending',
            ];
        }

        return $schedules;
    }
}
