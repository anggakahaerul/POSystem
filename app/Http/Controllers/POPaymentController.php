<?php

namespace App\Http\Controllers;

use App\Models\POPayment;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class POPaymentController extends Controller
{
    /**
     * Store payment schedule for a PO
     */
    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'termins' => 'required|array|min:1',
            'termins.*.description' => 'required|string|max:255',
            'termins.*.percentage' => 'required|numeric|min:1|max:100',
            'termins.*.due_date' => 'nullable|date',
        ], [
            'termins.required' => 'Minimal harus ada 1 termin pembayaran.',
            'termins.*.description.required' => 'Deskripsi termin wajib diisi.',
            'termins.*.percentage.required' => 'Persentase wajib diisi.',
        ]);

        // Validate total percentage = 100
        $totalPercentage = collect($validated['termins'])->sum('percentage');
        if ($totalPercentage != 100) {
            return back()->withInput()
                ->with('error', 'Total persentase harus 100%. Saat ini: ' . $totalPercentage . '%');
        }

        // Clear existing
        $purchaseOrder->payments()->delete();

        // Create new payment schedule
        $paymentNumber = 1;
        foreach ($validated['termins'] as $termin) {
            $amount = ($termin['percentage'] / 100) * $purchaseOrder->total_amount;
            
            POPayment::create([
                'purchase_order_id' => $purchaseOrder->id,
                'payment_number' => $paymentNumber,
                'description' => $termin['description'],
                'percentage' => $termin['percentage'],
                'amount' => $amount,
                'due_date' => $termin['due_date'] ?? $purchaseOrder->payment_due_date,
                'status' => 'pending',
            ]);

            $paymentNumber++;
        }

        return back()->with('success', 'Jadwal pembayaran berhasil disimpan.');
    }

    /**
     * Record payment for a specific termin
     */
    public function recordPayment(Request $request, POPayment $payment)
    {
        // Clean the amount before validation (remove money format)
        $request->merge([
            'paid_amount' => str_replace(['.', ','], '', $request->paid_amount ?? '0')
        ]);

        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:1',
            'note' => 'nullable|string',
        ], [
            'paid_amount.required' => 'Jumlah pembayaran wajib diisi.',
            'paid_amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'paid_amount.min' => 'Jumlah pembayaran minimal Rp 1.',
        ]);

        $paidAmount = (float) $validated['paid_amount'];
        
        // Check if amount exceeds remaining
        $remaining = $payment->amount - $payment->paid_amount;
        if ($paidAmount > $remaining) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa tagihan.');
        }

        // Record payment
        $payment->recordPayment($paidAmount, $validated['note'] ?? null);

        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    /**
     * Quick create standard termins (DP + Pelunasan)
     */
    public function quickCreate(Request $request, PurchaseOrder $purchaseOrder)
    {
        $type = $request->get('type', 'full');

        $termins = match($type) {
            'full' => [
                ['description' => 'Pembayaran Penuh', 'percentage' => 100],
            ],
            'dp50' => [
                ['description' => 'DP 50%', 'percentage' => 50],
                ['description' => 'Pelunasan 50%', 'percentage' => 50],
            ],
            'dp30' => [
                ['description' => 'DP 30%', 'percentage' => 30],
                ['description' => 'Pelunasan 70%', 'percentage' => 70],
            ],
            'termin3' => [
                ['description' => 'Termin 1 (30%)', 'percentage' => 30],
                ['description' => 'Termin 2 (30%)', 'percentage' => 30],
                ['description' => 'Pelunasan (40%)', 'percentage' => 40],
            ],
            default => [
                ['description' => 'Pembayaran Penuh', 'percentage' => 100],
            ],
        };

        // Clear existing
        $purchaseOrder->payments()->delete();

        // Create termins
        $paymentNumber = 1;
        foreach ($termins as $termin) {
            $amount = ($termin['percentage'] / 100) * $purchaseOrder->total_amount;
            
            POPayment::create([
                'purchase_order_id' => $purchaseOrder->id,
                'payment_number' => $paymentNumber,
                'description' => $termin['description'],
                'percentage' => $termin['percentage'],
                'amount' => $amount,
                'due_date' => $purchaseOrder->payment_due_date,
                'status' => 'pending',
            ]);

            $paymentNumber++;
        }

        return back()->with('success', 'Jadwal pembayaran berhasil dibuat.');
    }

    /**
     * Delete a payment termin
     */
    public function destroy(POPayment $payment)
    {
        if ($payment->paid_amount > 0) {
            return back()->with('error', 'Tidak dapat menghapus termin yang sudah ada pembayaran.');
        }

        $purchaseOrder = $payment->purchaseOrder;
        $payment->delete();

        // Update payment status
        $purchaseOrder->updatePaymentStatus();

        return back()->with('success', 'Termin pembayaran berhasil dihapus.');
    }
}
