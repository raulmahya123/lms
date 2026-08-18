<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(\Illuminate\Http\Request $r)
    {
        $filters = $r->validate([
            'q'        => ['nullable', 'string', 'max:100'],
            'status'   => ['nullable', Rule::in(['pending', 'paid', 'failed'])],
            'provider' => ['nullable', 'string', 'max:50'],
        ]);
        $term = trim((string) ($filters['q'] ?? ''));

        $items = \App\Models\Payment::query()
            ->select(['id', 'user_id', 'plan_id', 'course_id', 'amount', 'status', 'provider', 'reference', 'paid_at', 'created_at'])
            ->with(['user:id,name,email', 'plan:id,name', 'course:id,title'])
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($w) use ($term) {
                    $w->whereHas('user', function ($u) use ($term) {
                        $u->where('name', 'like', '%' . $term . '%')
                            ->orWhere('email', 'like', '%' . $term . '%');
                    })->orWhere('reference', 'like', '%' . $term . '%');
                });
            })
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['provider'] ?? null, fn($q, $provider) => $q->where('provider', $provider))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        // jika ingin dropdown provider unik di view
        $providers = \App\Models\Payment::query()
            ->select('provider')->whereNotNull('provider')->distinct()->pluck('provider');

        return view('admin.payments.index', compact('items', 'providers'));
    }


    public function show(Payment $payment)
    {
        $payment->load(['user:id,name,email', 'plan:id,name,price,period', 'course:id,title,price']);
        return view('admin.payments.show', compact('payment'));
    }

    public function update(Request $r, Payment $payment)
    {
        $data = $r->validate([
            'status'    => ['required', Rule::in(['pending', 'paid', 'failed'])],
            'reference' => 'nullable|string|max:100',
            'paid_at'   => 'nullable|date',
            'provider'  => 'nullable|string|max:50',
        ]);

        $payment->update($data);
        return back()->with('ok', 'Payment diupdate');
    }
}
