<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TestIq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestIqController extends Controller
{
    /**
     * Tampilkan halaman soal untuk user.
     * - Hanya test aktif
     * - Cek cool-down: bila belum waktunya, arahkan ke halaman hasil terakhir
     */
    public function show(TestIq $testIq)
    {
        abort_unless($testIq->is_active, 404);

        $userId = Auth::id();
        abort_unless($userId, 403);

        // CEK COOL-DOWN (pakai helper di model)
        $nextAt = $testIq->nextAvailableAtFor($userId);
        if ($nextAt->isFuture()) {
            // Belum boleh ikut lagi → arahkan ke halaman hasil & beri info kapan bisa ulang
            return redirect()
                ->route('user.test-iq.result', $testIq)
                ->with(
                    'status',
                    'Kamu baru bisa mengulang pada ' .
                    $nextAt->locale('id')->translatedFormat('d M Y H:i')
                );
        }

        // Boleh ikut sekarang
        return view('app.test_iq.show', [
            'test' => $testIq,
        ]);
    }

    /**
     * Simpan jawaban user.
     * - Validasi input
     * - Cek cool-down lagi (server-side)
     * - Hitung skor dan simpan ke kolom JSON submissions
     */
    public function submit(Request $r, TestIq $testIq)
    {
        abort_unless($testIq->is_active, 404);

        $userId = Auth::id();
        abort_unless($userId, 403);

        // Cegah submit kalau belum waktunya (hard check)
        if (! $testIq->canAttempt($userId)) {
            $nextAt = $testIq->nextAvailableAtFor($userId);
            return back()->with(
                'status',
                'Belum waktunya mengulang. Coba lagi pada ' .
                $nextAt->locale('id')->translatedFormat('d M Y H:i')
            );
        }

        $data = $r->validate([
            'answers'      => ['required', 'array'],
            'answers.*'    => ['nullable', 'string'],
            'duration_sec' => ['nullable', 'integer'],
        ]);

        // Hitung skor
        $questions = $testIq->questions ?? [];
        $correct   = 0;

        foreach ($questions as $q) {
            $qid = $q['id']     ?? null;
            $ans = $q['answer'] ?? null;

            if ($qid && isset($data['answers'][$qid]) && $data['answers'][$qid] === $ans) {
                $correct++;
            }
        }

        $score = $correct;

        // Simpan submission baru (append) ke JSON
        $subs   = $testIq->submissions ?? [];
        $subs[] = [
            'user_id'      => $userId,
            'score'        => $score,
            'answers'      => $data['answers'],
            'duration_sec' => $data['duration_sec'] ?? null,
            'submitted_at' => now()->toIso8601String(),
        ];

        $testIq->submissions = $subs;
        $testIq->save();

        return redirect()
            ->route('user.test-iq.result', $testIq)
            ->with('status', "Jawaban berhasil dikirim. Skor: {$score}/" . count($questions));
    }

    /**
     * Tampilkan hasil terakhir user + info kapan dapat mencoba lagi.
     */
    public function result(TestIq $testIq)
    {
        $userId = Auth::id();
        abort_unless($userId, 403);

        $subs = collect($testIq->submissions ?? [])
            ->where('user_id', $userId)
            ->values();

        $last   = $subs->isEmpty() ? null : $subs->last();
        $nextAt = $testIq->nextAvailableAtFor($userId);

        return view('app.test_iq.result', [
            'test'   => $testIq,
            'result' => $last,
            'nextAt' => $nextAt, // bisa dipakai untuk menampilkan countdown/kapan bisa tes lagi
        ]);
    }
}
