<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{
    PsyTest,
    PsyQuestion,
    PsyAttempt,
    PsyAnswer
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PsyQuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Helper: cari test aktif (slug atau id UUID) */
    private function resolveActiveTest(string $slugOrId): PsyTest
    {
        return PsyTest::query()
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('id', $slugOrId)->orWhere('slug', $slugOrId);
            })
            ->firstOrFail();
    }

    /**
     * GET /psy-tests/{slugOrId}/questions/{question}
     * Tampilkan 1 soal dari test aktif + prev/next + info waktu + prefill jawaban + progres.
     */
    public function show(Request $r, string $slugOrId, PsyQuestion $question)
    {
        // 1) Test aktif
        $test = $this->resolveActiveTest($slugOrId);

        // 2) Soal harus milik test
        abort_if($question->test_id !== $test->id, 404, 'Question not found in this test');

        // 3) Urutkan opsi (ordering → created_at)
        $question->load([
            'options' => fn ($q) => $q->orderBy('ordering')->orderBy('created_at'),
        ]);

        // 4) Siblings untuk prev/next (ordering → created_at)
        $siblings = $test->questions()
            ->orderBy('ordering')->orderBy('created_at')
            ->pluck('id')->all();

        $idx  = array_search($question->id, $siblings, true);
        $prev = ($idx !== false && $idx > 0) ? $siblings[$idx - 1] : null;
        $next = ($idx !== false && $idx < count($siblings) - 1) ? $siblings[$idx + 1] : null;

        // 5) Attempt aktif milik user (belum submit)
        $attempt = PsyAttempt::query()
            ->where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->first();

        // Jika belum ada attempt, arahkan untuk memulai
        if (!$attempt) {
            return redirect()->route('app.psy.attempts.start', $test->slug ?: $test->id);
        }

        // 6) Prefill jawaban user (jika ada)
        $answer = PsyAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->first();

        $selectedOptionId = $answer?->option_id;
        $typedValue       = $answer?->value;

        // 7) Progres (answered / total) + posisi sekarang
        $totalQuestions = count($siblings);
        $answeredIds    = PsyAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->pluck('question_id')->all();

        $answeredCount  = count($answeredIds);
        $current        = $idx === false ? 1 : ($idx + 1);
        $pct            = $totalQuestions ? (int) floor($answeredCount / $totalQuestions * 100) : 0;

        // 8) Waktu: pakai started_at dari attempt; fallback ke session seed
        $timeLimitMin = (int) ($test->time_limit_min ?? 0); // 0 = tanpa limit
        $secondsLeft  = null;
        $startedAtMs  = null;

        if ($timeLimitMin > 0) {
            if ($attempt->started_at) {
                $elapsed     = now()->diffInSeconds($attempt->started_at);
                $secondsLeft = max(0, $timeLimitMin * 60 - $elapsed);
                $startedAtMs = $attempt->started_at->valueOf();
            } else {
                // Fallback seed (harusnya jarang terjadi)
                $sessKey = "psy.{$test->getKey()}.started_at";
                if (!Session::has($sessKey)) {
                    Session::put($sessKey, now()->toIso8601String());
                }
                $startedAtIso = Session::get($sessKey);
                $elapsed      = now()->diffInSeconds(Carbon::parse($startedAtIso));
                $secondsLeft  = max(0, $timeLimitMin * 60 - $elapsed);
                $startedAtMs  = Carbon::parse($startedAtIso)->valueOf();
            }

            // Jika waktu habis di halaman soal → submit paksa
            if ($secondsLeft === 0) {
                return redirect()->route('app.psy.attempts.submit', $test->slug ?: $test->id);
            }
        }

        return view('app.psy_questions.show', [
            'test'         => $test,
            'question'     => $question,
            'prevId'       => $prev,
            'nextId'       => $next,

            // waktu
            'timeLimitMin' => $timeLimitMin,  // menit
            'startedAtMs'  => $startedAtMs,   // timestamp ms
            'secondsLeft'  => $secondsLeft,   // detik sisa (null jika tanpa limit)

            // attempt & prefill
            'attempt'          => $attempt,
            'selectedOptionId' => $selectedOptionId,
            'typedValue'       => $typedValue,

            // progres
            'current'       => $current,
            'total'         => $totalQuestions,
            'pct'           => $pct,
            'answeredIds'   => $answeredIds,
            'answeredCount' => $answeredCount,
        ]);
    }
}
