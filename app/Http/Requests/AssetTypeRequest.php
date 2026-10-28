<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('asset_type') ? ($this->route('asset_type')->id ?? $this->route('asset_type')) : null;

        return [
            'code' => 'required|string|max:50|unique:asset_types,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'badge_color' => 'nullable|string|in:primary,secondary,success,danger,warning,info,dark',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode tipe asset wajib diisi.',
            'code.unique' => 'Kode tipe asset sudah terdaftar.',
            'name.required' => 'Nama tipe asset wajib diisi.',
        ];
    }
}
