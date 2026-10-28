<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentTermRequest;
use App\Models\PaymentTerm;
use App\Services\PaymentTermService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    public function __construct(
        protected PaymentTermService $paymentTermService
    ) {}

    /**
     * Display a listing of payment terms.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $paymentTerms = $this->paymentTermService->getPaginatedList($filters, 10);

        return view('payment-terms.index', [
            'paymentTerms' => $paymentTerms,
            'moduleName' => 'Term Pembayaran',
            'moduleRoute' => 'payment-terms',
        ]);
    }

    /**
     * Store a newly created payment term.
     */
    public function store(PaymentTermRequest $request)
    {
        $term = $this->paymentTermService->createPaymentTerm($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Term pembayaran berhasil ditambahkan.',
                'data' => $term,
            ]);
        }

        return redirect()->route('payment-terms.index')
            ->with('success', 'Term pembayaran berhasil ditambahkan.');
    }

    /**
     * Display payment term data for modal.
     */
    public function show(PaymentTerm $paymentTerm): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $paymentTerm,
        ]);
    }

    /**
     * Show form for editing.
     */
    public function edit(PaymentTerm $paymentTerm, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $paymentTerm,
            ]);
        }

        return view('payment-terms.form', compact('paymentTerm'));
    }

    /**
     * Update the payment term.
     */
    public function update(PaymentTermRequest $request, PaymentTerm $paymentTerm)
    {
        $updated = $this->paymentTermService->updatePaymentTerm($paymentTerm->id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Term pembayaran berhasil diperbarui.',
                'data' => $updated,
            ]);
        }

        return redirect()->route('payment-terms.index')
            ->with('success', 'Term pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the payment term.
     */
    public function destroy(PaymentTerm $paymentTerm, Request $request)
    {
        $result = $this->paymentTermService->deletePaymentTerm($paymentTerm->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return redirect()->route('payment-terms.index')->with('error', $result['message']);
        }

        return redirect()->route('payment-terms.index')->with('success', $result['message']);
    }
}
