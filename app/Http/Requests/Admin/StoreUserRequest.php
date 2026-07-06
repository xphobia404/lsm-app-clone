<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->isAdmin() ?? false; }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'username'          => 'required|string|max:50|unique:users,username',
            'email'             => 'nullable|email|max:255|unique:users,email',
            'password'          => ['required', Password::min(6)],
            'course_type_ids'   => 'nullable|array',
            'course_type_ids.*' => 'exists:course_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
            'email.unique'       => 'Email sudah digunakan.',
            'password.required'  => 'Password wajib diisi.',
        ];
    }
}
