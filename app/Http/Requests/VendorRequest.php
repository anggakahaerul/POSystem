<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('vendor') ? ($this->route('vendor')->id ?? $this->route('vendor')) : null;

        return [
            'code' => 'required|string|max:20|unique:vendors,code,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'npwp_type' => 'nullable|string|in:Orang Pribadi,Badan,Instansi Pemerintah',
            'bank_info' => 'nullable|string',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode vendor wajib diisi.',
            'code.unique' => 'Kode vendor sudah digunakan.',
            'name.required' => 'Nama vendor wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
