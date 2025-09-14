<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PsyTest;
use App\Models\PsyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PsyQuestionController extends Controller
{
    /**
     * GET /psy-tests/{slugOrId}/questions/{question}
     * Tampilkan 1 soal dari test aktif + prev/next + info waktu.
     */
    public function show(Request $r, string|int $slugOrId, PsyQuestion $question)
    {
        // 1) Cari test aktif (slug atau id)
        $test = PsyTest::query()
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('id', $slugOrId)->orWhere('slug', $slugOrId);
            })
            ->firstOrFail();

        // 2) Pastikan soal milik test tsb
        abort_if($question->test_id !== $test->id, 404, 'Question not found in this test');

        // 3) Load opsi dengan urutan
        $question->load(['options' => fn($q) => $q->orderBy('ordering')->orderBy('id')]);

        // 4) Daftar id soal berurutan untuk prev/next
        $siblings = $test->questions()
            ->orderBy('ordering')->orderBy('id')
            ->pluck('id')->all();

        $idx  = array_search($question->id, $siblings, true);
        $prev = ($idx !== false && $idx > 0) ? $siblings[$idx - 1] : null;
        $next = ($idx !== false && $idx < count($siblings) - 1) ? $siblings[$idx + 1] : null;

        // 5) WAKTU: ambil limit dari test (menit), seed started_at di session
        $timeLimitMin = (int)($test->time_limit_min ?? 0); // 0 = tanpa limit
        $sessKey      = "psy.{$test->getKey()}.started_at";

        if ($timeLimitMin > 0 && !Session::has($sessKey)) {
            Session::put($sessKey, now()->toIso8601String());
        }

        $startedAtIso = $timeLimitMin > 0 ? Session::get($sessKey) : null;
        $startedAtMs  = $startedAtIso ? Carbon::parse($startedAtIso)->valueOf() : null;

        // Hitung sisa detik (kalau ada limit)
        $secondsLeft = null;
        if ($timeLimitMin > 0 && $startedAtIso) {
            $elapsed = now()->diffInSeconds(Carbon::parse($startedAtIso));
            $secondsLeft = max(0, $timeLimitMin * 60 - $elapsed);
        }

        return view('app.psy_questions.show', [
            'test'         => $test,
            'question'     => $question,
            'prevId'       => $prev,
            'nextId'       => $next,

            // VARIABEL BARU UNTUK WAKTU
            'timeLimitMin' => $timeLimitMin,  // integer menit
            'startedAtMs'  => $startedAtMs,   // timestamp ms (untuk countdown)
            'secondsLeft'  => $secondsLeft,   // detik sisa (null jika tanpa limit)
        ]);
    }
}
