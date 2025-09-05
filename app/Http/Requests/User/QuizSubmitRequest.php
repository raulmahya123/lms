<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class QuizSubmitRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'attempt_id' => ['required','integer','exists:quiz_attempts,id'],
            'answers'    => ['required','array'], // key: question_id => value (option_id|text)
        ];
    }
}
