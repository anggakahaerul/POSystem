<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category') ? ($this->route('category')->id ?? $this->route('category')) : null;

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'asset' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah terdaftar.',
            'asset.max' => 'Tipe asset maksimal 50 karakter.',
        ];
    }
}
