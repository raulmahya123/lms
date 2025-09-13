<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TestIq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class TestIqController extends Controller
{
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

    /** SHOW lama → redirect ke step 1 (biar link lama tetap jalan) */
    /** SHOW: tampilkan halaman pembuka (judul, deskripsi, jumlah soal, durasi, tombol Mulai) */
    public function show(TestIq $testIq)
    {
        abort_unless($testIq->is_active, 404);

        $userId = Auth::id();
        $already = collect($testIq->submissions ?? [])
            ->contains(fn($s) => ($s['user_id'] ?? null) === $userId);

        if ($already) {
            // kalau sudah pernah submit, langsung ke hasil
            return redirect()->route('user.test-iq.result', $testIq);
        }

        // tampilkan landing (bukan redirect ke step)
        return view('app.test_iq.start', [
            'test' => $testIq,
        ]);
    }


    /** START: /iq/{testIq}/start → reset dan ke step 1 */
    public function start(TestIq $testIq)
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
    public function showStep(TestIq $testIq, int $step)
    {
        abort_unless($testIq->is_active, 404);
        abort_unless(Auth::check(), 403);

        $questions = array_values($testIq->questions ?? []);
        $total = count($questions);
        abort_if($total === 0, 404);

        $step = max(1, min($step, $total));

        // seed started_at di session jika belum ada
        if (!session()->has($this->sessKey($testIq, 'started_at'))) {
            session([$this->sessKey($testIq, 'started_at') => now()]);
        }

        $q        = $questions[$step - 1] ?? [];
        $key      = $this->qKey($q, $step);
        $answers  = session($this->sessKey($testIq, 'answers'), []);
        $prevAnswer = $answers[$key] ?? null;

        $startedAt = session($this->sessKey($testIq, 'started_at'));
        $startedAtMs = \Carbon\Carbon::parse($startedAt)->valueOf();

        return view('app.test_iq.step', [
            'test'         => $testIq,
            'q'            => $q,
            'index'        => $step,
            'total'        => $total,
            'prevAnswer'   => $prevAnswer,
            'startedAtMs'  => $startedAtMs,   // <-- penting
        ]);
    }


    /** Simpan jawaban 1 soal & navigasi (prev/next/submit) */
    public function answer(Request $r, TestIq $testIq, int $step)
    {
        abort_unless($testIq->is_active, 404);
        abort_unless(Auth::check(), 403);

        $questions = array_values($testIq->questions ?? []);
        $total = count($questions);
        abort_if($total === 0, 404);

        $q = $questions[$step - 1] ?? null;
        abort_if(!$q, 422, 'Soal tidak ditemukan');

        $data = $r->validate([
            'answer' => ['nullable', 'string'],
            'nav'    => ['required', 'in:prev,next,submit'],
        ]);

        // simpan jawaban ke session dengan key aman (id/uuid/key/step)
        $key = $this->qKey($q, $step);
        $answers = session($this->sessKey($testIq, 'answers'), []);
        $answers[$key] = $data['answer'] ?? null;
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
    public function submit(Request $r, TestIq $testIq)
    {
        abort_unless($testIq->is_active, 404);

        $userId = Auth::id();
        abort_unless($userId, 403);

        $answers = session($this->sessKey($testIq, 'answers'));
        if (!is_array($answers)) {
            // fallback kalau ada form lama
            $answers = $r->input('answers', []);
        }

        $startedAt = session($this->sessKey($testIq, 'started_at'));
        $durationSec = $startedAt ? now()->diffInSeconds($startedAt) : null;

        $questions = array_values($testIq->questions ?? []);
        $total = count($questions);

        // siapkan lookup jawaban benar: by id/uuid/key dan juga by step (fallback)
        $byKey = [];
        foreach ($questions as $i => $q) {
            $step = $i + 1;
            $key  = $this->qKey($q, $step);
            $byKey[$key] = $q['answer'] ?? null;
        }

        $correct = 0;
        foreach ($answers as $key => $ans) {
            $right = $byKey[$key] ?? null;
            if ($right !== null && $ans === $right) {
                $correct++;
            }
        }

        $subs = $testIq->submissions ?? [];
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

    /** Tampilkan hasil user */
    public function result(TestIq $testIq)
    {
        $userId = Auth::id();
        abort_unless($userId, 403);

        $subs = collect($testIq->submissions ?? [])->where('user_id', $userId)->values();
        $last = $subs->isEmpty() ? null : $subs->last();

        return view('app.test_iq.result', [
            'test'   => $testIq,
            'result' => $last,
        ]);
    }
}
