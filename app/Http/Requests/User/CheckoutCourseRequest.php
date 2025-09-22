<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Harus login untuk pakai kupon
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Kode kupon: string
            'code'      => ['required', 'string', 'max:64'],

            // Nominal harga yang sedang dihitung di checkout (rupiah)
            // Simpan sebagai integer (mis. 150000 = Rp 150.000)
            'amount'    => ['required', 'integer', 'min:0'],

            // ⬇️ INI YANG PENTING: Course kamu pakai UUID, bukan integer
            'course_id' => ['nullable', 'uuid'],

            // Kalau kamu juga punya kupon untuk plan, sama-sama UUID
            'plan_id'   => ['nullable', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.uuid' => 'Course ID harus berupa UUID yang valid.',
            'plan_id.uuid'   => 'Plan ID harus berupa UUID yang valid.',
        ];
    }
}
