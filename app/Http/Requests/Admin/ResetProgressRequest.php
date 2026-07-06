<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResetProgressRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->isAdmin() ?? false; }

    public function rules(): array
    {
        return [
            'course_type_id' => 'required|exists:course_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'course_type_id.required' => 'Pilih spesialisasi yang ingin direset.',
            'course_type_id.exists'   => 'Spesialisasi tidak ditemukan.',
        ];
    }
}
