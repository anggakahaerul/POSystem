<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('department') ? ($this->route('department')->id ?? $this->route('department')) : null;

        return [
            'code' => 'required|string|max:30|unique:departments,code,' . $id,
            'name' => 'required|string|max:255',
            'manager_l3_id' => 'nullable|exists:users,id',
            'manager_l4_id' => 'nullable|exists:users,id',
            'manager_l5_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'branches' => 'nullable|array',
            'branches.*.branch_id' => 'required_with:branches|exists:branches,id',
            'branches.*.manager_l3_id' => 'nullable|exists:users,id',
            'branches.*.manager_l4_id' => 'nullable|exists:users,id',
            'branches.*.manager_l5_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode departemen wajib diisi.',
            'code.unique' => 'Kode departemen sudah digunakan.',
            'name.required' => 'Nama departemen wajib diisi.',
            'manager_l3_id.exists' => 'User Manager Level 3 yang dipilih tidak valid.',
            'manager_l4_id.exists' => 'User Manager Level 4 yang dipilih tidak valid.',
            'manager_l5_id.exists' => 'User Manager Level 5 yang dipilih tidak valid.',
        ];
    }
}
