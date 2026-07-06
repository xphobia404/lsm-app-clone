<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool { return auth()->user()?->isAdmin() ?? false; }

    public function rules(): array
    {
        $filledOptions = ['a'];
        foreach (['b', 'c', 'd'] as $key) {
            if ($this->filled('option_' . $key)) {
                $filledOptions[] = $key;
            }
        }

        return [
            'question'       => 'required|string',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'nullable|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => ['required', 'in:' . implode(',', $filledOptions)],
            'explanation'    => 'nullable|string',
            'order'          => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required'       => 'Pertanyaan wajib diisi.',
            'option_a.required'       => 'Pilihan A wajib diisi.',
            'correct_answer.required' => 'Jawaban benar wajib dipilih.',
            'correct_answer.in'       => 'Jawaban benar harus dipilih dari opsi yang sudah diisi.',
        ];
    }
}
