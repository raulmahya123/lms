<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Sanitasi biar 422 nggak terjadi gara-gara tipe/empty string.
     */
    protected function prepareForValidation(): void
    {
        $code = trim((string) $this->input('code', ''));
        $amount = $this->input('amount', 0);

        // ubah empty string -> null utk UUID field
        $courseId = $this->input('course_id');
        $planId   = $this->input('plan_id');

        $this->merge([
            'code'      => $code,
            'amount'    => is_numeric($amount) ? (int) $amount : 0,
            'course_id' => $courseId !== '' ? $courseId : null,
            'plan_id'   => $planId !== '' ? $planId : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'code'      => ['required', 'string', 'max:64'],
            'amount'    => ['required', 'integer', 'min:0'],    // rupiah int
            'course_id' => ['nullable', 'uuid'],               // PK kamu UUID
            'plan_id'   => ['nullable', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.uuid' => 'Course ID harus UUID yang valid.',
            'plan_id.uuid'   => 'Plan ID harus UUID yang valid.',
        ];
    }
}
