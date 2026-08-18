<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PsyAnswer;
use App\Models\PsyAttempt;
use App\Models\PsyQuestion;
use App\Services\Psychology\PsyTestFlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PsyAttemptController extends Controller
{
    public function __construct(private PsyTestFlowService $psyFlow)
    {
        $this->middleware('auth');
    }

    public function start(Request $request, string $slugOrId)
    {
        $test = $this->psyFlow
            ->resolveActiveTest($slugOrId, ['id', 'name', 'slug', 'is_active', 'time_limit_min'])
            ->loadCount('questions');

        abort_if(($test->questions_count ?? 0) === 0, 422, 'Tes belum memiliki pertanyaan.');

        $attempt = $this->psyFlow->startOrResumeAttempt((string) Auth::id(), $test->id);

        if ($this->psyFlow->timeLeftSec($attempt, $test) === 0) {
            $attempt = $this->psyFlow->finalizeAttempt($attempt, $test);
            return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
        }

        $nextId = $this->psyFlow->nextUnansweredQuestionId($test, $attempt);
        if (!$nextId) {
            $attempt = $this->psyFlow->finalizeAttempt($attempt, $test);
            return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
        }

        return redirect()->route('app.psytests.questions.show', [$test->slug ?: $test->id, $nextId]);
    }

    public function answer(Request $request, string $slugOrId, PsyQuestion $question)
    {
        $test = $this->psyFlow->resolveActiveTest($slugOrId, ['id', 'name', 'slug', 'is_active', 'time_limit_min']);
        abort_unless($question->test_id === $test->id, 404);

        $attempt = $this->psyFlow->activeAttempt((string) Auth::id(), $test->id);
        abort_unless($attempt, 404);

        if ($this->psyFlow->timeLeftSec($attempt, $test) === 0) {
            $attempt = $this->psyFlow->finalizeAttempt($attempt, $test);
            return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
        }

        $hasOptions = $question->options()->exists();
        if ($hasOptions) {
            $data = $request->validate([
                'option_id' => [
                    'required',
                    'uuid',
                    Rule::exists('psy_options', 'id')->where('question_id', $question->id),
                ],
            ]);
            $optionId = $data['option_id'];
            $value = null;
        } else {
            $data = $request->validate([
                'value' => ['required', 'numeric'],
            ]);
            $optionId = null;
            $value = (int) $data['value'];
        }

        PsyAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $question->id],
            ['option_id' => $optionId, 'value' => $value]
        );

        $next = $this->psyFlow->nextQuestionId($test, $question->id);

        return $next
            ? redirect()->route('app.psytests.questions.show', [$test->slug ?: $test->id, $next])
            : redirect()->route('app.psy.attempts.submit', $test->slug ?: $test->id);
    }

    public function submit(string $slugOrId)
    {
        $test = $this->psyFlow->resolveActiveTest($slugOrId, ['id', 'name', 'slug', 'is_active', 'time_limit_min']);

        $attempt = PsyAttempt::query()
            ->where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->with(['answers.question', 'answers.option'])
            ->firstOrFail();

        $attempt = $this->psyFlow->finalizeAttempt($attempt, $test);

        return redirect()->route('app.psy.attempts.result', [$test->slug ?: $test->id, $attempt]);
    }

    public function result(string $slugOrId, PsyAttempt $attempt)
    {
        $test = $this->psyFlow->resolveActiveTest($slugOrId, ['id', 'name', 'slug', 'is_active', 'time_limit_min']);
        abort_unless($attempt->user_id === Auth::id() && $attempt->test_id === $test->id, 404);

        if (is_null($attempt->submitted_at)) {
            $attempt = $this->psyFlow->finalizeAttempt($attempt, $test);
        }

        $attempt->load([
            'answers' => fn ($q) => $q->select(['id', 'attempt_id', 'question_id', 'option_id', 'value'])->orderBy('id'),
            'answers.question:id,prompt,trait_key,qtype',
            'answers.option:id,label,value',
        ]);

        $scoresArr = is_array($attempt->score_json)
            ? $attempt->score_json
            : (array) json_decode($attempt->score_json ?? '[]', true);

        $total = (int) ($scoresArr['_total'] ?? $attempt->total_score ?? 0);
        $profile = $this->psyFlow->profileForScore($test->id, $total);
        ['min' => $rangeMin, 'max' => $rangeMax] = $this->psyFlow->profileRange($test->id);

        $percentile = null;
        if (is_numeric($rangeMin) && is_numeric($rangeMax) && $rangeMax > $rangeMin) {
            $percentile = (int) round(100 * ($total - $rangeMin) / ($rangeMax - $rangeMin));
            $percentile = max(0, min(100, $percentile));
        }

        $test->loadCount('questions');

        return view('app.psy_attempts.result', [
            'test' => $test,
            'attempt' => $attempt,
            'scores' => $scoresArr,
            'traits' => collect($scoresArr)->except('_total'),
            'total' => $total,
            'profile' => $attempt->result_key,
            'profileKey' => optional($profile)->key,
            'profileName' => optional($profile)->name,
            'reco' => $attempt->recommendation_text,
            'recoText' => optional($profile)->description ?: 'Profil belum terdefinisi untuk rentang skor ini.',
            'answers' => $attempt->answers,
            'durationSec' => ($attempt->started_at && $attempt->submitted_at)
                ? $attempt->submitted_at->diffInSeconds($attempt->started_at)
                : null,
            'percentile' => $percentile,
        ]);
    }
}

