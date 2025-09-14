<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{
    PsyTest, PsyQuestion, PsyOption,
    PsyAttempt, PsyAnswer, PsyProfile
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PsyAttemptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Helper: resolve test aktif dari slug atau id. */
    private function resolveActiveTest(string|int $slugOrId): PsyTest
    {
        return PsyTest::query()
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('id', $slugOrId)->orWhere('slug', $slugOrId);
            })
            ->firstOrFail();
    }

    /** Hitung sisa detik berdasarkan time_limit_min test (null kalau tidak dibatasi) */
    private function timeLeftSec(?PsyAttempt $attempt, PsyTest $test): ?int
    {
        $limitMin = (int)($test->time_limit_min ?? 0);
        if (!$attempt || $limitMin <= 0 || !$attempt->started_at) {
            return null;
        }
        $elapsed = now()->diffInSeconds($attempt->started_at);
        $left    = max(0, ($limitMin * 60) - $elapsed);
        return $left;
    }

    /** Finalisasi attempt: hitung skor, pilih profil, set submitted_at */
    private function finalizeAttempt(PsyAttempt $attempt, PsyTest $test): PsyAttempt
    {
        return DB::transaction(function () use ($attempt, $test) {
            // reload answers lengkap saat finalisasi
            $attempt->load(['answers.question', 'answers.option']);

            // Skor per-trait & total
            $traitTotals = [];
            $traitCounts = [];
            $total       = 0;

            foreach ($attempt->answers as $ans) {
                // jika ada jawaban yang "nyasar" (bukan milik test ini), abaikan hardening
                if ($ans->question && $ans->question->test_id !== $test->id) {
                    continue;
                }

                $trait = $ans->question?->trait_key ?: 'general';
                $val   = !is_null($ans->value)
                    ? (int) $ans->value
                    : (int) optional($ans->option)->value;

                $traitTotals[$trait] = ($traitTotals[$trait] ?? 0) + $val;
                $traitCounts[$trait] = ($traitCounts[$trait] ?? 0) + 1;
                $total += $val;
            }

            $scores = [];
            foreach ($traitTotals as $trait => $sum) {
                $cnt = max(1, $traitCounts[$trait]);
                $scores[$trait] = round($sum / $cnt, 2);
            }
            $scores['_total'] = $total;

            // Profil cocok (min_total..max_total)
            $profile = PsyProfile::where('test_id', $test->id)
                ->where('min_total', '<=', $total)
                ->where('max_total', '>=', $total)
                ->orderBy('min_total', 'desc')
                ->first();

            // Set hasil akhir
            $attempt->score_json          = $scores;
            $attempt->result_key          = $profile?->key;
            $attempt->recommendation_text = $profile
                ? ($profile->name . ($profile->description ? ' — ' . $profile->description : ''))
                : 'Profil belum terdefinisi untuk rentang skor ini.';
            $attempt->submitted_at        = now();
            $attempt->save();

            return $attempt;
        });
    }

    /** Mulai / resume attempt */
    public function start(Request $r, string|int $slugOrId)
    {
        $test = $this->resolveActiveTest($slugOrId)->loadCount('questions');
        abort_if($test->questions_count === 0, 422, 'Tes belum memiliki pertanyaan.');

        $attempt = PsyAttempt::firstOrCreate(
            ['user_id' => Auth::id(), 'test_id' => $test->id, 'submitted_at' => null],
            ['started_at' => now()]
        );

        // Jika sudah melewati time limit, langsung finalisasi
        $left = $this->timeLeftSec($attempt, $test);
        if ($left === 0) {
            $attempt = $this->finalizeAttempt($attempt, $test);
            return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
        }

        // Soal pertama / berikutnya yang belum dijawab
        $qIds     = $test->questions()->orderBy('ordering')->orderBy('id')->pluck('id')->all();
        $answered = PsyAnswer::where('attempt_id', $attempt->id)->pluck('question_id')->all();
        $nextId   = collect($qIds)->first(fn($id) => !in_array($id, $answered, true)) ?? end($qIds);

        return redirect()->route('app.psytests.questions.show', [$test->slug ?: $test->id, $nextId]);
    }

    /** Simpan jawaban 1 soal dan maju */
    public function answer(Request $r, string|int $slugOrId, PsyQuestion $question)
    {
        $test = $this->resolveActiveTest($slugOrId);

        // Soal harus milik test ini
        abort_unless($question->test_id === $test->id, 404);

        // Attempt aktif milik user
        $attempt = PsyAttempt::where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        // Cek time limit: kalau habis saat submit jawaban, finalisasi langsung
        $left = $this->timeLeftSec($attempt, $test);
        if ($left === 0) {
            $attempt = $this->finalizeAttempt($attempt, $test);
            return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
        }

        // Validasi: jika question punya options → wajib option_id yang belongTo question
        $hasOptions = $question->exists && $question->options()->exists();

        $rules = [
            'option_id' => ['nullable', 'integer'],
            'value'     => ['nullable', 'integer'],
        ];
        $r->validate($rules);

        // Hardening: pastikan option (jika ada) memang milik question ini
        $optionId = $r->input('option_id');
        if ($hasOptions) {
            // Jika pakai opsi, value bebas boleh null; option_id harus valid & milik question
            abort_if(
                is_null($optionId) ||
                !PsyOption::where('id', $optionId)->where('question_id', $question->id)->exists(),
                422,
                'Pilihan tidak valid.'
            );
        } else {
            // Kalau tidak pakai opsi, wajib ada value integer; kosongkan option_id
            abort_if(!is_numeric($r->input('value')), 422, 'Nilai jawaban tidak valid.');
            $optionId = null;
        }

        PsyAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $question->id],
            ['option_id'  => $optionId, 'value' => $hasOptions ? null : (int) $r->input('value')]
        );

        // Tentukan next question
        $qIds = $test->questions()->orderBy('ordering')->orderBy('id')->pluck('id')->all();
        $idx  = array_search($question->id, $qIds, true);
        $next = ($idx !== false && $idx < count($qIds) - 1) ? $qIds[$idx + 1] : null;

        return $next
            ? redirect()->route('app.psytests.questions.show', [$test->slug ?: $test->id, $next])
            : redirect()->route('app.psy.attempts.submit', $test->slug ?: $test->id);
    }

    /** Tutup attempt, hitung skor & pilih profil */
    public function submit(string|int $slugOrId)
    {
        $test = $this->resolveActiveTest($slugOrId);

        $attempt = PsyAttempt::where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->with(['answers.question', 'answers.option'])
            ->firstOrFail();

        // Finalisasi (termasuk kalau time limit sudah habis)
        $attempt = $this->finalizeAttempt($attempt, $test);

        return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
    }

    /** Halaman hasil (lengkap) */
    public function result(string|int $slugOrId, PsyAttempt $attempt)
    {
        $test = $this->resolveActiveTest($slugOrId);

        // Attempt harus milik user & test cocok
        abort_unless($attempt->user_id === Auth::id() && $attempt->test_id === $test->id, 404);

        // Jika attempt belum disubmit (misal user langsung akses URL), finalisasi dulu
        if (is_null($attempt->submitted_at)) {
            $attempt = $this->finalizeAttempt($attempt, $test);
        }

        // Eager load untuk rekap jawaban
        $attempt->load([
            'answers' => fn($q) => $q->orderBy('id'),
            'answers.question:id,prompt,trait_key,qtype',
            'answers.option:id,label,value',
        ]);

        // Skor & total
        $scoresArr = is_array($attempt->score_json)
            ? $attempt->score_json
            : (array) json_decode($attempt->score_json ?? '[]', true);

        $total  = (int) ($scoresArr['_total'] ?? 0);
        $traits = collect($scoresArr)->except('_total');

        // Profil detail (key, name, description)
        $profile = PsyProfile::where('test_id', $test->id)
            ->where('min_total', '<=', $total)
            ->where('max_total', '>=', $total)
            ->orderBy('min_total', 'desc')
            ->first();

        // Durasi (detik)
        $durationSec = ($attempt->started_at && $attempt->submitted_at)
            ? $attempt->submitted_at->diffInSeconds($attempt->started_at)
            : null;

        // Persentil kasar berdasarkan rentang min/max psy_profiles
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

            // skor & profil
            'scores'        => $scoresArr,
            'traits'        => $traits,
            'total'         => $total,
            'profile'       => $attempt->result_key, // tetap kirim yang lama untuk backward-compat
            'profileKey'    => optional($profile)->key,
            'profileName'   => optional($profile)->name,
            'reco'          => $attempt->recommendation_text, // lama
            'recoText'      => optional($profile)->description ?: 'Profil belum terdefinisi untuk rentang skor ini.',

            // tambahan lengkap
            'answers'       => $attempt->answers,
            'durationSec'   => $durationSec,
            'percentile'    => $percentile,
        ]);
    }
}
