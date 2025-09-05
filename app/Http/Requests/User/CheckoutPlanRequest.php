<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutPlanRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'coupon_code' => ['nullable','string'],
        ];
    }
}
