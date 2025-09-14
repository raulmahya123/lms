<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TestIq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class TestIqController extends Controller
{
    /**
     * (Opsional) Batas percobaan per "season" & durasinya.
     * Set ke 0 untuk menonaktifkan limiter.
     */
    private const MAX_ATTEMPTS_PER_SEASON = 1;     // contoh: 1x per season
    private const SEASON_SECONDS          = 86400; // 24 jam

    /** Session key helper */
    private function sessKey(TestIq $t, string $suffix): string
    {
        return "iq.{$t->getKey()}.$suffix";
    }

    /** Buat key jawaban per soal (prioritas id/uuid/key, fallback: nomor step) */
    private function qKey(array $q, int $step): string
    {
        return (string)($q['id'] ?? $q['uuid'] ?? $q['key'] ?? $step);
    }

    /** SHOW: halaman pembuka (judul, deskripsi, jumlah soal, durasi, tombol Mulai) */
    public function show(TestIq $testIq): View|RedirectResponse
    {
        abort_unless($testIq->is_active, 404);

        $userId  = Auth::id();
        $already = collect($testIq->submissions ?? [])
            ->contains(fn($s) => ($s['user_id'] ?? null) === $userId);

        if ($already) {
            // kalau sudah pernah submit, langsung ke hasil
            return redirect()->route('user.test-iq.result', $testIq);
        }

        // tampilkan landing
        return view('app.test_iq.start', [
            'test' => $testIq,
        ]);
    }

    /** START: /iq/{testIq}/start → reset dan ke step 1 */
    public function start(TestIq $testIq): RedirectResponse
    {
        abort_unless($testIq->is_active, 404);
        abort_unless(Auth::check(), 403);

        session()->forget([
            $this->sessKey($testIq, 'answers'),
            $this->sessKey($testIq, 'started_at'),
        ]);
        session([$this->sessKey($testIq, 'started_at') => now()]);

        return redirect()->route('user.test-iq.question', [$testIq, 1]);
    }

    /** Tampilkan 1 soal (step) */
    public function showStep(TestIq $testIq, int $step): View
    {
        abort_unless($testIq->is_active, 404);
        abort_unless(Auth::check(), 403);

        $questions = array_values($testIq->questions ?? []);
        $total     = count($questions);
        abort_if($total === 0, 404);

        $step = max(1, min($step, $total));

        // seed started_at di session jika belum ada
        if (!session()->has($this->sessKey($testIq, 'started_at'))) {
            session([$this->sessKey($testIq, 'started_at') => now()]);
        }

        $q          = $questions[$step - 1] ?? [];
        $key        = $this->qKey($q, $step);
        $answers    = session($this->sessKey($testIq, 'answers'), []);
        $prevAnswer = $answers[$key] ?? null;

        $startedAt   = session($this->sessKey($testIq, 'started_at'));
        $startedAtMs = Carbon::parse($startedAt)->valueOf();

        return view('app.test_iq.step', [
            'test'        => $testIq,
            'q'           => $q,
            'index'       => $step,
            'total'       => $total,
            'prevAnswer'  => $prevAnswer,
            'startedAtMs' => $startedAtMs,   // untuk countdown di front-end
        ]);
    }

    /** Simpan jawaban 1 soal & navigasi (prev/next/submit) */
    public function answer(Request $r, TestIq $testIq, int $step): RedirectResponse
    {
        abort_unless($testIq->is_active, 404);
        abort_unless(Auth::check(), 403);

        $questions = array_values($testIq->questions ?? []);
        $total     = count($questions);
        abort_if($total === 0, 404);

        $q = $questions[$step - 1] ?? null;
        abort_if(!$q, 422, 'Soal tidak ditemukan');

        $data = $r->validate([
            'answer' => ['nullable', 'string'],
            'nav'    => ['required', 'in:prev,next,submit'],
        ]);

        // simpan jawaban ke session dengan key aman (id/uuid/key/step)
        $key             = $this->qKey($q, $step);
        $answers         = session($this->sessKey($testIq, 'answers'), []);
        $answers[$key]   = $data['answer'] ?? null;
        session([$this->sessKey($testIq, 'answers') => $answers]);

        if ($data['nav'] === 'prev') {
            $prev = max(1, $step - 1);
            return redirect()->route('user.test-iq.question', [$testIq, $prev]);
        }

        if ($data['nav'] === 'next') {
            $next = min($total, $step + 1);
            return redirect()->route('user.test-iq.question', [$testIq, $next]);
        }

        // submit akhir
        return $this->submit($r, $testIq);
    }

    /** Simpan semua & hitung skor (dipanggil saat submit akhir) */
    public function submit(Request $r, TestIq $testIq): RedirectResponse
    {
        abort_unless($testIq->is_active, 404);

        $userId = Auth::id();
        abort_unless($userId, 403);

        $answers = session($this->sessKey($testIq, 'answers'));
        if (!is_array($answers)) {
            // fallback kalau ada form lama
            $answers = $r->input('answers', []);
        }

        $startedAt   = session($this->sessKey($testIq, 'started_at'));
        $durationSec = $startedAt ? now()->diffInSeconds($startedAt) : null;

        $questions = array_values($testIq->questions ?? []);
        $total     = count($questions);

        // siapkan lookup jawaban benar: by id/uuid/key dan juga by step (fallback)
        $byKey = [];
        foreach ($questions as $i => $q) {
            $step       = $i + 1;
            $key        = $this->qKey($q, $step);
            $byKey[$key]= $q['answer'] ?? null;
        }

        $correct = 0;
        foreach ($answers as $key => $ans) {
            $right = $byKey[$key] ?? null;
            if ($right !== null && $ans === $right) {
                $correct++;
            }
        }

        $subs   = $testIq->submissions ?? [];
        $subs[] = [
            'user_id'      => $userId,
            'score'        => $correct,
            'answers'      => $answers,
            'duration_sec' => $durationSec,
            'submitted_at' => now()->toIso8601String(),
        ];

        $testIq->submissions = $subs;
        $testIq->save();

        // bersihkan session attempt
        session()->forget([
            $this->sessKey($testIq, 'answers'),
            $this->sessKey($testIq, 'started_at'),
        ]);

        return redirect()
            ->route('user.test-iq.result', $testIq)
            ->with('status', "Jawaban terkirim. Skor: {$correct}/{$total}");
    }

    /**
     * Tampilkan hasil terakhir user + info kapan dapat mencoba lagi.
     */
    public function result(TestIq $testIq): View
    {
        $userId = Auth::id();
        abort_unless($userId, 403);

        $subs = collect($testIq->submissions ?? [])
            ->where('user_id', $userId)
            ->values();

        $last   = $subs->isEmpty() ? null : $subs->last();
        $nextAt = null; // <-- penting: inisialisasi supaya tidak undefined

        // Jika limiter diaktifkan, hitung kapan boleh tes lagi
        if (self::MAX_ATTEMPTS_PER_SEASON > 0 && self::SEASON_SECONDS > 0) {
            $windowStart    = now()->subSeconds(self::SEASON_SECONDS);
            $recentAttempts = $subs->filter(function ($s) use ($windowStart) {
                $ts = $s['submitted_at'] ?? null;
                return $ts && Carbon::parse($ts)->greaterThan($windowStart);
            });

            if ($recentAttempts->count() >= self::MAX_ATTEMPTS_PER_SEASON) {
                $lastInWindow = $recentAttempts->last();
                if ($lastInWindow && !empty($lastInWindow['submitted_at'])) {
                    $lastAt = Carbon::parse($lastInWindow['submitted_at']);
                    $nextAt = $lastAt->copy()->addSeconds(self::SEASON_SECONDS);
                }
            }
        }

        return view('app.test_iq.result', [
            'test'   => $testIq,
            'result' => $last,
            'nextAt' => $nextAt, // Carbon|null
        ]);
    }
}
