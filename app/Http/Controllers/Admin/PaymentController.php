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
        $items = \App\Models\Payment::query()
            ->with(['user:id,name,email', 'plan:id,name', 'course:id,title'])
            ->when($r->filled('q'), function ($q) use ($r) {
                $q->whereHas('user', function ($u) use ($r) {
                    $u->where('name', 'like', '%' . $r->q . '%')
                        ->orWhere('email', 'like', '%' . $r->q . '%');
                })
                    ->orWhere('reference', 'like', '%' . $r->q . '%')     // jika ada kolom reference/invoice
                    ->orWhere('invoice', 'like', '%' . $r->q . '%');      // sesuaikan
            })
            ->when($r->filled('status'), fn($q) => $q->where('status', $r->status))
            ->when($r->filled('provider'), fn($q) => $q->where('provider', $r->provider))
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
        $payment->load(['user:id,name,email', 'plan:id,name', 'course:id,title']);
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
