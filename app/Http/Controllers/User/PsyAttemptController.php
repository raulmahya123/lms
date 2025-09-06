<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{
    PsyTest, PsyQuestion, PsyOption,
    PsyAttempt, PsyAnswer, PsyProfile
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PsyAttemptController extends Controller
{
    /**
     * Helper: resolve test aktif dari slug atau id.
     */
    private function resolveActiveTest(string|int $slugOrId): PsyTest
    {
        return PsyTest::query()
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('id', $slugOrId)->orWhere('slug', $slugOrId);
            })
            ->firstOrFail();
    }

    /**
     * Mulai / resume attempt untuk sebuah test.
     * POST /psy-tests/{slugOrId}/start
     */
    public function start(Request $r, string|int $slugOrId)
    {
        $test = $this->resolveActiveTest($slugOrId);

        $attempt = PsyAttempt::firstOrCreate(
            ['user_id' => Auth::id(), 'test_id' => $test->id, 'submitted_at' => null],
            ['started_at' => now()]
        );

        // Arahkan ke soal pertama (atau berikutnya yg belum dijawab)
        $qIds     = $test->questions()->orderBy('ordering')->orderBy('id')->pluck('id')->all();
        $answered = PsyAnswer::where('attempt_id', $attempt->id)->pluck('question_id')->all();
        $nextId   = collect($qIds)->first(fn($id) => !in_array($id, $answered, true)) ?? end($qIds);

        return redirect()->route('app.psytests.questions.show', [$test->slug ?: $test->id, $nextId]);
    }

    /**
     * Simpan jawaban untuk 1 soal dan maju ke soal berikutnya.
     * POST /psy-tests/{slugOrId}/q/{question}/answer
     *
     * Body:
     * - option_id (untuk MCQ / Likert dengan opsi)
     * - value     (untuk Likert tanpa opsi; integer, contoh 1..5 atau -2..+2 sesuai skema)
     */
    public function answer(Request $r, string|int $slugOrId, PsyQuestion $question)
    {
        $test = $this->resolveActiveTest($slugOrId);

        $r->validate([
            'option_id' => ['nullable','integer','exists:psy_options,id'],
            'value'     => ['nullable','integer'],
        ]);

        // Pastikan question milik test
        abort_unless($question->test_id === $test->id, 404);

        // Pastikan ada attempt ONGOING
        $attempt = PsyAttempt::where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        PsyAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $question->id],
            ['option_id'  => $r->option_id, 'value' => $r->value]
        );

        // Ke soal berikutnya
        $qIds = $test->questions()->orderBy('ordering')->orderBy('id')->pluck('id')->all();
        $idx  = array_search($question->id, $qIds, true);
        $next = ($idx !== false && $idx < count($qIds)-1) ? $qIds[$idx+1] : null;

        return $next
            ? redirect()->route('app.psytests.questions.show', [$test->slug ?: $test->id, $next])
            : redirect()->route('app.psy.attempts.submit', $test->slug ?: $test->id); // selesai → submit
    }

    /**
     * Tutup attempt, hitung skor & pilih profil rekomendasi.
     * GET /psy-tests/{slugOrId}/submit
     */
    public function submit(string|int $slugOrId)
    {
        $test = $this->resolveActiveTest($slugOrId);

        $attempt = PsyAttempt::where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->with(['answers.question','answers.option'])
            ->firstOrFail();

        // Hitung skor per-trait & total
        $traitTotals = [];
        $traitCounts = [];
        $total       = 0;

        foreach ($attempt->answers as $ans) {
            $trait = $ans->question->trait_key ?: 'general';
            // nilai: value langsung, atau ambil dari option->value
            $val   = !is_null($ans->value) ? (int)$ans->value : (int)optional($ans->option)->value;

            $traitTotals[$trait] = ($traitTotals[$trait] ?? 0) + $val;
            $traitCounts[$trait] = ($traitCounts[$trait] ?? 0) + 1;
            $total += $val;
        }

        // Buat score_json (rata-rata per trait; _total untuk agregat)
        $scores = [];
        foreach ($traitTotals as $trait => $sum) {
            $cnt = max(1, $traitCounts[$trait]);
            $scores[$trait] = round($sum / $cnt, 2);
        }
        $scores['_total'] = $total;

        // Cari profil cocok berdasarkan total di psy_profiles (min_total..max_total)
        $profile = PsyProfile::where('test_id', $test->id)
            ->where('min_total', '<=', $total)
            ->where('max_total', '>=', $total)
            ->orderBy('min_total', 'desc')
            ->first();

        // Set hasil
        $attempt->score_json          = $scores;
        $attempt->result_key          = $profile?->key;
        $attempt->recommendation_text = $profile
            ? ($profile->name . ($profile->description ? ' — '.$profile->description : ''))
            : 'Profil belum terdefinisi untuk rentang skor ini.';
        $attempt->submitted_at        = now();
        $attempt->save();

        return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
    }

    /**
     * Tampilkan halaman hasil attempt (skor + rekomendasi).
     * GET /psy-tests/{slugOrId}/result/{attempt}
     */
    public function result(string|int $slugOrId, PsyAttempt $attempt)
    {
        $test = $this->resolveActiveTest($slugOrId);

        abort_unless($attempt->user_id === Auth::id() && $attempt->test_id === $test->id, 404);
        $test->loadCount('questions');

        return view('app.psy_attempts.result', [
            'test'    => $test,
            'attempt' => $attempt,
            'scores'  => $attempt->score_json ?? [],
            'total'   => ($attempt->score_json['_total'] ?? 0),
            'profile' => $attempt->result_key,
            'reco'    => $attempt->recommendation_text,
        ]);
    }
}
