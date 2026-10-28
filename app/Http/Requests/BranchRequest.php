<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('branch') ? ($this->route('branch')->id ?? $this->route('branch')) : null;

        return [
            'code' => 'required|string|max:20|unique:branches,code,' . $id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'npwp' => 'nullable|string|max:30',
            'logo_base64' => 'nullable|string',
            'footer_note' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode cabang wajib diisi.',
            'code.unique' => 'Kode cabang sudah digunakan.',
            'name.required' => 'Nama cabang wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
