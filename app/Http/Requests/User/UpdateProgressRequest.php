<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'progress'  => ['nullable','array'],
            'completed' => ['nullable','boolean'],
        ];
    }
}
