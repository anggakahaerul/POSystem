<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'payment_type' => 'required|in:full,partial',
            'days' => 'nullable|integer|min:0',
            'installments' => 'nullable',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama term pembayaran wajib diisi.',
            'payment_type.required' => 'Tipe pembayaran wajib dipilih.',
            'payment_type.in' => 'Tipe pembayaran harus Full atau Partial.',
        ];
    }
}
