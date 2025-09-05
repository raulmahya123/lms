<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $items = Payment::with(['plan:id,name','course:id,title'])
            ->where('user_id', Auth::id())
            ->latest('id')->paginate(20);
        return view('app.payments.index', compact('items'));
    }

    public function show(Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id(), 403);
        $payment->load(['plan','course']);
        return view('app.payments.show', compact('payment'));
    }
}
