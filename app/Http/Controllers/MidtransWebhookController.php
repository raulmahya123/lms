<?php

namespace App\Http\Controllers;

use App\Models\{Payment, Membership, Plan};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Transaction;

use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
       public function ping()
    {
        return response()->json(['ok' => true, 'service' => 'midtrans'], 200);
    }

    // POST /midtrans/webhook -> untuk notifikasi Midtrans
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('midtrans.webhook', $payload);

        // Verifikasi signature
        $serverKey = config('services.midtrans.server_key');
        $expected = hash('sha512',
            ($payload['order_id'] ?? '').
            ($payload['status_code'] ?? '').
            ($payload['gross_amount'] ?? '').
            $serverKey
        );
        if (($payload['signature_key'] ?? '') !== $expected) {
            Log::warning('midtrans.signature_mismatch', ['order_id' => $payload['order_id'] ?? null]);
            return response('invalid signature', 403);
        }

        // Update tabel payments
        $payment = Payment::where('reference', $payload['order_id'] ?? '')->first();
        if (!$payment) return response('not found', 404);

        $trx = $payload['transaction_status'] ?? 'pending';
        if (in_array($trx, ['capture','settlement'])) {
            $payment->status  = 'paid';
            $payment->paid_at = now();
        } elseif (in_array($trx, ['cancel','deny','expire'])) {
            $payment->status = 'failed';
        } else {
            $payment->status = 'pending';
        }
        $payment->provider = 'midtrans';
        $payment->save();

        return response('OK', 200);
    }
    private function activateMembership(Payment $payment): void
    {
        $membership = null;

        if ($payment->membership_id) {
            $membership = Membership::find($payment->membership_id);
        }

        if (!$membership && $payment->plan_id && $payment->user_id) {
            $membership = Membership::where('user_id', $payment->user_id)
                ->where('plan_id', $payment->plan_id)
                ->where('status', 'pending')
                ->latest('id')
                ->first();
        }

        if (!$membership) {
            Log::info('No membership to activate', ['payment_id' => $payment->id]);
            return;
        }

        $plan = Plan::find($membership->plan_id);
        $now  = Carbon::now();
        $expiresAt = match ($plan?->period) {
            'yearly' => $now->copy()->addYear(),
            default  => $now->copy()->addMonth(),
        };

        $membership->update([
            'status'       => 'active',
            'activated_at' => $membership->activated_at ?: $now,
            'expires_at'   => $expiresAt,
        ]);

        Log::info('Membership activated', [
            'membership_id' => $membership->id,
            'payment_id'    => $payment->id
        ]);
    }
    public function __invoke(Request $request)
    {
        // 1) Ping untuk tombol "Test" (GET)
        if ($request->isMethod('get')) {
            return response()->json(['ok' => true, 'service' => 'midtrans'], 200);
        }

        // 2) Proses notifikasi asli (POST)
        $payload = $request->all();
        Log::info('midtrans.webhook', $payload);

        // --- verifikasi signature (sangat direkomendasikan) ---
        $serverKey = config('services.midtrans.server_key');
        $expected = hash(
            'sha512',
            ($payload['order_id'] ?? '') .
                ($payload['status_code'] ?? '') .
                ($payload['gross_amount'] ?? '') .
                $serverKey
        );
        if (($payload['signature_key'] ?? '') !== $expected) {
            Log::warning('midtrans.signature_mismatch', ['order_id' => $payload['order_id'] ?? null]);
            return response('invalid signature', 403);
        }

        // --- mapping status ke tabel payments ---
        $payment = Payment::where('reference', $payload['order_id'] ?? '')->first();
        if (!$payment) return response('not found', 404);

        $status = $payload['transaction_status'] ?? 'pending';
        if (in_array($status, ['capture', 'settlement'])) {
            $payment->status = 'paid';
            $payment->paid_at = now();
        } elseif (in_array($status, ['cancel', 'deny', 'expire'])) {
            $payment->status = 'failed';
        } else {
            $payment->status = 'pending';
        }
        $payment->provider = 'midtrans';
        $payment->save();

        return response('OK', 200);
    }
}
