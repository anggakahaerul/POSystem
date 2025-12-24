<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    /**
     * Display a listing of payment terms.
     */
    public function index(Request $request)
    {
        $query = PaymentTerm::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $paymentTerms = $query->orderBy('payment_type')
            ->orderBy('days')
            ->paginate(10)
            ->withQueryString();

        return view('payment-terms.index', compact('paymentTerms'));
    }

    /**
     * Show the form for creating a new payment term.
     */
    public function create()
    {
        return view('payment-terms.form', [
            'paymentTerm' => null,
        ]);
    }

    /**
     * Store a newly created payment term.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'payment_type' => 'required|in:full,partial',
            'days' => 'required_if:payment_type,full|integer|min:0',
            'installments' => 'required_if:payment_type,partial',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama term pembayaran wajib diisi.',
            'payment_type.required' => 'Tipe pembayaran wajib dipilih.',
            'days.required_if' => 'Jumlah hari wajib diisi untuk pembayaran penuh.',
            'installments.required_if' => 'Konfigurasi cicilan wajib diisi.',
        ]);

        $data = [
            'name' => $validated['name'],
            'payment_type' => $validated['payment_type'],
            'days' => $validated['payment_type'] === 'full' ? ($validated['days'] ?? 0) : 0,
            'installments' => $validated['payment_type'] === 'partial' ? $validated['installments'] : null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        PaymentTerm::create($data);

        return redirect()->route('payment-terms.index')
            ->with('success', 'Term pembayaran berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the payment term.
     */
    public function edit(PaymentTerm $paymentTerm)
    {
        return view('payment-terms.form', compact('paymentTerm'));
    }

    /**
     * Update the payment term.
     */
    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'payment_type' => 'required|in:full,partial',
            'days' => 'required_if:payment_type,full|integer|min:0',
            'installments' => 'required_if:payment_type,partial',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'payment_type' => $validated['payment_type'],
            'days' => $validated['payment_type'] === 'full' ? ($validated['days'] ?? 0) : 0,
            'installments' => $validated['payment_type'] === 'partial' ? $validated['installments'] : null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        $paymentTerm->update($data);

        return redirect()->route('payment-terms.index')
            ->with('success', 'Term pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the payment term.
     */
    public function destroy(PaymentTerm $paymentTerm)
    {
        if ($paymentTerm->purchaseOrders()->count() > 0) {
            return redirect()->route('payment-terms.index')
                ->with('error', 'Term pembayaran masih digunakan di PO.');
        }

        $paymentTerm->delete();

        return redirect()->route('payment-terms.index')
            ->with('success', 'Term pembayaran berhasil dihapus.');
    }
}
