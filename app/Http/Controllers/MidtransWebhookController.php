<?php

namespace App\Http\Controllers;

use App\Models\{Payment, Membership, Plan};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('midtrans.webhook.incoming', $payload);

        // ---- Signature check ----
        $serverKey = config('services.midtrans.server_key');
        $expected = hash('sha512',
            ($payload['order_id'] ?? '') .
            ($payload['status_code'] ?? '') .
            ($payload['gross_amount'] ?? '') .
            $serverKey
        );

        if (!hash_equals($expected, (string) ($payload['signature_key'] ?? ''))) {
            Log::warning('midtrans.webhook.signature_mismatch', [
                'order_id' => $payload['order_id'] ?? null,
            ]);
            return response()->json(['message' => 'invalid signature'], 403);
        }

        $orderId   = $payload['order_id'] ?? null;           // = payments.reference
        $trxStatus = $payload['transaction_status'] ?? '';   // capture/settlement/pending/deny/expire/cancel/failure
        $fraud     = $payload['fraud_status'] ?? '';         // accept/challenge
        $gross     = $payload['gross_amount'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'order_id missing'], 422);
        }

        DB::transaction(function () use ($orderId, $trxStatus, $fraud, $gross) {
            // Cari payment by reference (order_id)
            $payment = Payment::where('reference', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                Log::warning('midtrans.webhook.payment_not_found', ['order_id' => $orderId]);
                return; // idempotent: tidak error 5xx
            }

            // Map status
            $newStatus = match ($trxStatus) {
                'capture'    => ($fraud === 'challenge') ? 'pending' : 'paid',
                'settlement' => 'paid',
                'pending'    => 'pending',
                'cancel', 'deny', 'expire', 'failure' => 'failed',
                default      => 'pending',
            };

            // Idempotent
            if ($payment->status === $newStatus) {
                return;
            }

            // Update payment
            $payment->provider  = 'midtrans';
            $payment->status    = $newStatus;
            // optional konsistensi jumlah
            if ($gross !== null && is_numeric($gross)) {
                $payment->amount = (int) $gross;
            }
            if ($newStatus === 'paid') {
                $payment->paid_at = now();
            }
            $payment->save();

            // Bila sudah paid dan ada plan → update membership
            if ($newStatus === 'paid' && $payment->plan_id) {
                $plan = Plan::find($payment->plan_id);
                if ($plan) {
                    $m = Membership::firstOrNew(['user_id' => $payment->user_id]);

                    $now  = Carbon::now();
                    $from = ($m->exists && $m->expires_at && $m->expires_at->isFuture())
                        ? $m->expires_at
                        : $now;

                    $expires = ($plan->period === 'yearly')
                        ? $from->copy()->addYear()
                        : $from->copy()->addMonth();

                    $m->plan_id      = $plan->id;
                    $m->status       = 'active';
                    $m->activated_at = $m->exists && $m->activated_at ? $m->activated_at : $now;
                    $m->expires_at   = $expires;
                    $m->save();

                    // Link back
                    $payment->membership_id = $m->id;
                    $payment->save();
                }
            }
        });

        return response()->json(['ok' => true]);
    }
}
