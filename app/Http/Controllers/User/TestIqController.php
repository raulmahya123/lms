<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TestIq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TestIqController extends Controller
{
    /** Session key helper */
    private function sessKey(TestIq $t, string $suffix): string
    {
        return "iq.{$t->getKey()}.$suffix";
    }

    /** Key jawaban per soal (prioritas id/uuid/key, fallback: nomor step) */
    private function qKey(array $q, int $step): string
    {
        return (string)($q['id'] ?? $q['uuid'] ?? $q['key'] ?? $step);
    }

    /** Helper: ambil daftar soal yang sudah dibatasi oleh meta.max_questions (jika ada) */
    private function limitedQuestions(TestIq $testIq): array
    {
        $all    = array_values($testIq->questions ?? []);
        $limit  = (int) data_get($testIq, 'meta.max_questions', 0);
        return $limit > 0 ? array_slice($all, 0, $limit) : $all;
    }

    /** SHOW: landing + cek cooldown via Model (tanpa detik hardcode) */
    public function show(TestIq $testIq): View|RedirectResponse
    {
        abort_unless($testIq->is_active, 404);
        $userId = Auth::id();

        $nextAt = $testIq->nextAvailableAtFor((string)$userId);
        if (now()->lessThan($nextAt)) {
            // masih cooldown → langsung ke hasil terakhir
            return redirect()->route('user.test-iq.result', $testIq);
        }

        return view('app.test_iq.start', ['test' => $testIq]);
    }

    /** START: reset dan ke step 1 (pakai cooldown dari Model) */
    public function start(TestIq $testIq): RedirectResponse
    {
        abort_unless($testIq->is_active, 404);
        abort_unless(Auth::check(), 403);

        $userId = (string)Auth::id();
        $nextAt = $testIq->nextAvailableAtFor($userId);
        if (now()->lessThan($nextAt)) {
            return redirect()->route('user.test-iq.result', $testIq);
        }

        // bersihkan sesi
        session()->forget([
            $this->sessKey($testIq, 'answers'),
            $this->sessKey($testIq, 'started_at'),
        ]);

        session([
            $this->sessKey($testIq, 'started_at') => now(),
            'iq.in_progress'  => $testIq->getKey(),
            'iq.current_step' => 1,
            'iq.return_to'    => route('user.test-iq.question', [$testIq->getKey(), 1]),
        ]);

        return redirect()->route('user.test-iq.question', [$testIq, 1]);
    }

    /** Tampilkan 1 soal (pakai meta.max_questions) */
    public function showStep(TestIq $testIq, int $step): View
    {
        abort_unless($testIq->is_active, 404);
        abort_unless(Auth::check(), 403);

        $questions = $this->limitedQuestions($testIq);
        $total     = count($questions);
        abort_if($total === 0, 404);

        $step = max(1, min($step, $total));

        if (!session()->has($this->sessKey($testIq, 'started_at'))) {
            session([$this->sessKey($testIq, 'started_at') => now()]);
        }

        $q          = $questions[$step - 1] ?? [];
        $key        = $this->qKey($q, $step);
        $answers    = session($this->sessKey($testIq, 'answers'), []);
        $prevAnswer = $answers[$key] ?? null;

        $startedAt   = session($this->sessKey($testIq, 'started_at'));
        $startedAtMs = Carbon::parse($startedAt)->valueOf();

        session([
            'iq.current_step' => $step,
            'iq.return_to'    => route('user.test-iq.question', [$testIq->getKey(), $step]),
        ]);

        return view('app.test_iq.step', [
            'test'        => $testIq,
            'q'           => $q,
            'index'       => $step,
            'total'       => $total,
            'prevAnswer'  => $prevAnswer,
            'startedAtMs' => $startedAtMs,
        ]);
    }

    /** Simpan jawaban & navigasi */
    public function answer(Request $r, TestIq $testIq, int $step): RedirectResponse
    {
        abort_unless($testIq->is_active, 404);
        abort_unless(Auth::check(), 403);

        $questions = $this->limitedQuestions($testIq);
        $total     = count($questions);
        abort_if($total === 0, 404);

        $q = $questions[$step - 1] ?? null;
        abort_if(!$q, 422, 'Soal tidak ditemukan');

        $r->validate(['nav' => ['required', Rule::in(['prev','next','submit'])]]);

        $key     = (string)$this->qKey($q, $step);
        $options = array_values($q['options'] ?? []);

        // Ambil nilai jawaban dari berbagai bentuk nama field
        $rawAnswer = $r->input('answer');
        if ($rawAnswer === null) $rawAnswer = $r->input("answers.$key");
        if ($rawAnswer === null) $rawAnswer = $r->input("answers.$step");

        // Normalisasi -> index
        $idxAnswer = null;
        if (!empty($options)) {
            if ($rawAnswer === '' || $rawAnswer === null) {
                $idxAnswer = null;
            } elseif (is_numeric($rawAnswer)) {
                $n = (int)$rawAnswer;
                if ($n >= 0 && $n < count($options)) $idxAnswer = $n;
            } else {
                $pos = array_search((string)$rawAnswer, $options, true);
                if ($pos !== false) $idxAnswer = (int)$pos;
            }
        }

        // simpan ke session
        $answers       = session($this->sessKey($testIq, 'answers'), []);
        $answers[$key] = $idxAnswer;
        session([$this->sessKey($testIq, 'answers') => $answers]);

        // navigasi
        $nav = $r->input('nav');
        if ($nav === 'prev')  return redirect()->route('user.test-iq.question', [$testIq, max(1, $step - 1)]);
        if ($nav === 'next')  return redirect()->route('user.test-iq.question', [$testIq, min($total, $step + 1)]);
        return $this->submit($r, $testIq);
    }

    /** Submit akhir: simpan & hitung skor + estimasi IQ (pakai meta.norm_table) */
    public function submit(Request $r, TestIq $testIq): RedirectResponse
    {
        abort_unless($testIq->is_active, 404);
        $userId = Auth::id();
        abort_unless($userId, 403);

        $answers = session($this->sessKey($testIq, 'answers'));
        if (!is_array($answers)) {
            $answers = (array)$r->input('answers', []);
        }

        $startedAt   = session($this->sessKey($testIq, 'started_at'));
        $durationSec = $startedAt ? now()->diffInSeconds($startedAt) : null;

        $questions = $this->limitedQuestions($testIq);
        $total     = count($questions);

        // siapkan lookup jawaban benar
        $byKey     = [];
        $optsByKey = [];
        foreach ($questions as $i => $q) {
            $step            = $i + 1;
            $key             = $this->qKey($q, $step);
            $optsByKey[$key] = array_values($q['options'] ?? []);
            $byKey[$key]     = array_key_exists('answer_index', $q) ? $q['answer_index'] : null;
            // fallback: 'answer' string -> index
            if ($byKey[$key] === null && isset($q['answer']) && is_string($q['answer'])) {
                $pos = array_search($q['answer'], $optsByKey[$key], true);
                $byKey[$key] = ($pos !== false) ? (int)$pos : null;
            }
        }

        // normalisasi jawaban user: string -> index
        foreach ($answers as $key => $ans) {
            if ($ans === null || is_int($ans)) continue;
            if (is_numeric($ans)) {
                $answers[$key] = (int)$ans;
                continue;
            }
            $pos = array_search((string)$ans, $optsByKey[$key] ?? [], true);
            $answers[$key] = ($pos !== false) ? (int)$pos : null;
        }

        // hitung benar
        $correct = 0;
        foreach ($answers as $key => $ans) {
            if ($ans === null) continue;
            $right = $byKey[$key] ?? null;
            if ($right !== null && (int)$ans === (int)$right) $correct++;
        }

        // ambil norm table dari meta
        $normTable = data_get($testIq, 'meta.norm_table');
        $iqMeta    = $this->estimateIq($correct, $total, is_array($normTable) ? $normTable : null);

        // simpan submission (append ke JSON)
        $subs   = $testIq->submissions ?? [];
        $subs[] = [
            'user_id'       => $userId,
            'raw_correct'   => $correct,
            'total'         => $total,
            'percent'       => $iqMeta['percent'],
            'estimated_iq'  => $iqMeta['iq'],
            'band'          => $iqMeta['band'],
            'answers'       => $answers,
            'duration_sec'  => $durationSec,
            'submitted_at'  => now()->toIso8601String(),
        ];
        $testIq->submissions = $subs;
        $testIq->save();

        // beresin session
        session()->forget([
            $this->sessKey($testIq, 'answers'),
            $this->sessKey($testIq, 'started_at'),
            'iq.in_progress',
            'iq.current_step',
            'iq.return_to',
        ]);

        return redirect()
            ->route('user.test-iq.result', $testIq)
            ->with('status', "Jawaban terkirim. Skor: {$correct}/{$total} · Estimasi IQ: {$iqMeta['iq']} ({$iqMeta['band']})");
    }

    /** Hasil terakhir + kapan bisa tes lagi (pakai nextAvailableAtFor) */
    public function result(TestIq $testIq): View
    {
        $userId = Auth::id();
        abort_unless($userId, 403);

        $subs  = collect($testIq->submissions ?? [])->where('user_id', $userId)->values();
        $last  = $subs->isEmpty() ? null : $subs->last();
        $nextAt = $testIq->nextAvailableAtFor((string)$userId);

        return view('app.test_iq.result', [
            'test'   => $testIq,
            'result' => $last,
            'nextAt' => $nextAt,
        ]);
    }

    /**
     * Konversi jumlah benar → estimasi IQ.
     * - Jika ada norm_table: lookup min_raw tertinggi <= correct.
     * - Jika tidak: fallback linear (0%→70, 50%→100, 100%→145).
     */
    private function estimateIq(int $correct, int $total, ?array $normTable = null): array
    {
        $percent = $total > 0 ? ($correct / $total) * 100 : 0;

        if ($normTable) {
            $iq = 70;
            foreach ($normTable as $row) {
                if (isset($row['min_raw'], $row['iq']) && $correct >= (int)$row['min_raw']) {
                    $iq = (int)$row['iq'];
                }
            }
            $iq = max(55, min(160, $iq));
        } else {
            $iq = (int) round(70 + 0.75 * $percent); // 0%:70, 50%:100, 100%:145
            $iq = max(55, min(160, $iq));
        }

        $band = match (true) {
            $iq >= 130 => 'Very Superior',
            $iq >= 120 => 'Superior',
            $iq >= 110 => 'High Average',
            $iq >= 90  => 'Average',
            $iq >= 80  => 'Low Average',
            default    => 'Borderline/Below',
        };

        return [
            'percent' => round($percent, 2),
            'iq'      => $iq,
            'band'    => $band,
        ];
    }
}
