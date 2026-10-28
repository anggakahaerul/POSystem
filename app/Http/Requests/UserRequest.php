<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user') ? ($this->route('user')->id ?? $this->route('user')) : null;
        $isUpdate = !empty($id);

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => $isUpdate ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'required|exists:roles,name',
            'department_id' => 'nullable|exists:departments,id',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role pengguna wajib dipilih.',
        ];
    }
}
