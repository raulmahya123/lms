<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{
    PsyTest,
    PsyQuestion,
    PsyOption,
    PsyAttempt,
    PsyAnswer,
    PsyProfile
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PsyAttemptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Resolve test aktif dari slug atau UUID. */
    private function resolveActiveTest(string $slugOrId): PsyTest
    {
        return PsyTest::query()
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('id', $slugOrId)->orWhere('slug', $slugOrId);
            })
            ->firstOrFail();
    }

    /** Hitung sisa detik sesuai time_limit_min (null jika tanpa limit). */
    private function timeLeftSec(?PsyAttempt $attempt, PsyTest $test): ?int
    {
        $limitMin = (int) ($test->time_limit_min ?? 0);
        if (!$attempt || $limitMin <= 0 || !$attempt->started_at) return null;

        $elapsed = now()->diffInSeconds($attempt->started_at);
        return max(0, ($limitMin * 60) - $elapsed);
    }

    /**
     * Finalisasi:
     * - hitung skor per-trait (rata-rata) dan total
     * - pilih profil dari psy_profiles secara dinamis (exact → bawah → atas)
     * - simpan score_json, total_score, result_key, recommendation_text, submitted_at
     */
    private function finalizeAttempt(PsyAttempt $attempt, PsyTest $test): PsyAttempt
    {
        return DB::transaction(function () use ($attempt, $test) {
            $attempt->loadMissing(['answers.question', 'answers.option']);

            $traitTotals = [];
            $traitCounts = [];
            $total       = 0;

            foreach ($attempt->answers as $ans) {
                if (!$ans->question || $ans->question->test_id !== $test->id) continue;

                $trait = $ans->question->trait_key ?: 'general';
                $val   = (int) ($ans->score ?? (
                    !is_null($ans->value) ? (int)$ans->value : (int)($ans->option->value ?? 0)
                ));

                $traitTotals[$trait] = ($traitTotals[$trait] ?? 0) + $val;
                $traitCounts[$trait] = ($traitCounts[$trait] ?? 0) + 1;
                $total += $val;
            }

            // Rata-rata per trait
            $scores = [];
            foreach ($traitTotals as $trait => $sum) {
                $cnt = max(1, $traitCounts[$trait]);
                $scores[$trait] = round($sum / $cnt, 2);
            }
            $scores['_total'] = $total;

            // Cari profil dinamis
            $profile = PsyProfile::query()
                ->where('test_id', $test->id)
                ->where('min_total', '<=', $total)
                ->where('max_total', '>=', $total)
                ->orderBy('min_total', 'desc')
                ->first();

            if (!$profile) {
                $profile = PsyProfile::query()
                    ->where('test_id', $test->id)
                    ->where('min_total', '<=', $total)
                    ->orderBy('min_total', 'desc')
                    ->first();
            }
            if (!$profile) {
                $profile = PsyProfile::query()
                    ->where('test_id', $test->id)
                    ->where('max_total', '>=', $total)
                    ->orderBy('max_total', 'asc')
                    ->first();
            }

            $attempt->score_json          = $scores;   // pastikan cast di model
            $attempt->total_score         = $total;
            $attempt->result_key          = $profile?->key;
            $attempt->recommendation_text = $profile
                ? trim($profile->name . ($profile->description ? ' — ' . $profile->description : ''))
                : 'Profil belum terdefinisi untuk rentang skor ini.';
            $attempt->submitted_at        = now();
            $attempt->save();

            return $attempt;
        });
    }

    /** Mulai / resume attempt → redirect ke soal berikutnya yang belum dijawab. */
    public function start(Request $r, string $slugOrId)
    {
        $test = $this->resolveActiveTest($slugOrId)->loadCount('questions');
        abort_if(($test->questions_count ?? 0) === 0, 422, 'Tes belum memiliki pertanyaan.');

        $attempt = PsyAttempt::firstOrCreate(
            ['user_id' => Auth::id(), 'test_id' => $test->id, 'submitted_at' => null],
            ['started_at' => now()]
        );

        // Jika waktu habis saat dibuka
        $left = $this->timeLeftSec($attempt, $test);
        if ($left === 0) {
            $attempt = $this->finalizeAttempt($attempt, $test);
            return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
        }

        // Soal berikutnya yang belum dijawab
        $qIds     = $test->questions()->orderBy('ordering')->orderBy('created_at')->pluck('id')->all();
        $answered = PsyAnswer::where('attempt_id', $attempt->id)->pluck('question_id')->all();
        $nextId   = collect($qIds)->first(fn ($id) => !in_array($id, $answered, true)) ?? ($qIds[0] ?? null);

        if (!$nextId) {
            $attempt = $this->finalizeAttempt($attempt, $test);
            return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
        }

        // 🔁 FIX: pakai nama route yang benar
        return redirect()->route('app.psytests.questions.show', [$test->slug ?: $test->id, $nextId]);
    }

    /** Simpan jawaban 1 soal lalu maju. */
    public function answer(Request $r, string $slugOrId, PsyQuestion $question)
    {
        $test = $this->resolveActiveTest($slugOrId);
        abort_unless($question->test_id === $test->id, 404);

        $attempt = PsyAttempt::where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        // time limit habis saat submit → finalisasi
        $left = $this->timeLeftSec($attempt, $test);
        if ($left === 0) {
            $attempt = $this->finalizeAttempt($attempt, $test);
            return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
        }

        $hasOptions = $question->options()->exists();

        if ($hasOptions) {
            $data = $r->validate([
                'option_id' => [
                    'required',
                    'uuid',
                    Rule::exists('psy_options', 'id')->where('question_id', $question->id),
                ],
            ]);
            $optionId = $data['option_id'];
            $value    = null;
        } else {
            $data = $r->validate([
                'value' => ['required', 'numeric'],
            ]);
            $optionId = null;
            $value    = (int) $data['value'];
        }

        PsyAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $question->id],
            ['option_id'  => $optionId, 'value' => $value]
        );

        // Next question
        $qIds = $test->questions()->orderBy('ordering')->orderBy('created_at')->pluck('id')->all();
        $idx  = array_search($question->id, $qIds, true);
        $next = ($idx !== false && $idx < count($qIds) - 1) ? $qIds[$idx + 1] : null;

        // 🔁 FIX: pakai nama route yang benar
        return $next
            ? redirect()->route('app.psytests.questions.show', [$test->slug ?: $test->id, $next])
            : redirect()->route('app.psy.attempts.submit', $test->slug ?: $test->id);
    }

    /** Submit akhir → finalize & redirect ke result. */
    public function submit(string $slugOrId)
    {
        $test = $this->resolveActiveTest($slugOrId);

        $attempt = PsyAttempt::where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->with(['answers.question', 'answers.option'])
            ->firstOrFail();

        $attempt = $this->finalizeAttempt($attempt, $test);

        return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
    }

    /** Halaman hasil (lengkap). */
    public function result(string $slugOrId, PsyAttempt $attempt)
    {
        $test = $this->resolveActiveTest($slugOrId);
        abort_unless($attempt->user_id === Auth::id() && $attempt->test_id === $test->id, 404);

        if (is_null($attempt->submitted_at)) {
            $attempt = $this->finalizeAttempt($attempt, $test);
        }

        $attempt->load([
            'answers' => fn ($q) => $q->orderBy('id'),
            'answers.question:id,prompt,trait_key,qtype',
            'answers.option:id,label,value',
        ]);

        $scoresArr = is_array($attempt->score_json)
            ? $attempt->score_json
            : (array) json_decode($attempt->score_json ?? '[]', true);

        $total  = (int) ($scoresArr['_total'] ?? $attempt->total_score ?? 0);
        $traits = collect($scoresArr)->except('_total');

        $profile = PsyProfile::where('test_id', $test->id)
            ->where('min_total', '<=', $total)
            ->where('max_total', '>=', $total)
            ->orderBy('min_total', 'desc')
            ->first();

        $rangeMin = PsyProfile::where('test_id', $test->id)->min('min_total');
        $rangeMax = PsyProfile::where('test_id', $test->id)->max('max_total');
        $percentile = null;
        if (is_numeric($rangeMin) && is_numeric($rangeMax) && $rangeMax > $rangeMin) {
            $percentile = (int) round(100 * ($total - $rangeMin) / ($rangeMax - $rangeMin));
            $percentile = max(0, min(100, $percentile));
        }

        $test->loadCount('questions');

        return view('app.psy_attempts.result', [
            'test'          => $test,
            'attempt'       => $attempt,
            'scores'        => $scoresArr,
            'traits'        => $traits,
            'total'         => $total,
            'profile'       => $attempt->result_key,
            'profileKey'    => optional($profile)->key,
            'profileName'   => optional($profile)->name,
            'reco'          => $attempt->recommendation_text,
            'recoText'      => optional($profile)->description ?: 'Profil belum terdefinisi untuk rentang skor ini.',
            'answers'       => $attempt->answers,
            'durationSec'   => ($attempt->started_at && $attempt->submitted_at)
                                ? $attempt->submitted_at->diffInSeconds($attempt->started_at)
                                : null,
            'percentile'    => $percentile,
        ]);
    }
}
