<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('product') ? ($this->route('product')->id ?? $this->route('product')) : null;

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $id,
            'unit' => 'required|string|max:20',
            'price' => 'nullable',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'name.required' => 'Nama produk wajib diisi.',
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU sudah digunakan oleh produk lain.',
            'unit.required' => 'Satuan (unit) wajib diisi.',
        ];
    }
}
