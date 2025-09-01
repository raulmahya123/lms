<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $r)
    {
        $items = Payment::with(['user:id,name,email','plan:id,name','course:id,title'])
            ->when($r->filled('status'), fn($q)=>$q->where('status',$r->status))
            ->latest('id')->paginate(20);

        return view('admin.payments.index', compact('items'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user:id,name,email','plan:id,name','course:id,title']);
        return view('admin.payments.show', compact('payment'));
    }

    public function update(Request $r, Payment $payment)
    {
        $data = $r->validate([
            'status'    => ['required', Rule::in(['pending','paid','failed'])],
            'reference' => 'nullable|string|max:100',
            'paid_at'   => 'nullable|date',
            'provider'  => 'nullable|string|max:50',
        ]);

        $payment->update($data);
        return back()->with('ok','Payment diupdate');
    }
}
