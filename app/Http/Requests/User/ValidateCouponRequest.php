<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCouponRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'code'      => ['required','string'],
            'context'   => ['nullable','in:plan,course'],
            'plan_id'   => ['nullable','integer'],
            'course_id' => ['nullable','integer'],
            'amount'    => ['nullable','numeric','min:0'],
        ];
    }
}
