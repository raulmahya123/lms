<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PsyAnswer;
use App\Models\PsyQuestion;
use App\Services\Psychology\PsyTestFlowService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PsyQuestionController extends Controller
{
    public function __construct(private PsyTestFlowService $psyFlow)
    {
        $this->middleware('auth');
    }

    public function show(Request $request, string $slugOrId, PsyQuestion $question)
    {
        $test = $this->psyFlow->resolveActiveTest($slugOrId, ['id', 'name', 'slug', 'is_active', 'time_limit_min']);
        abort_if($question->test_id !== $test->id, 404, 'Question not found in this test');

        $question->load([
            'options' => fn ($q) => $q
                ->select(['id', 'question_id', 'label', 'value', 'ordering', 'created_at'])
                ->orderBy('ordering')
                ->orderBy('created_at'),
        ]);

        $attempt = $this->psyFlow->activeAttempt((string) Auth::id(), $test->id);
        if (!$attempt) {
            return redirect()->route('app.psy.attempts.start', $test->slug ?: $test->id);
        }

        $answer = PsyAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->first(['id', 'attempt_id', 'question_id', 'option_id', 'value']);

        $progress = $this->psyFlow->questionProgress($test, $attempt, $question->id);

        $timeLimitMin = (int) ($test->time_limit_min ?? 0);
        $secondsLeft = null;
        $startedAtMs = null;

        if ($timeLimitMin > 0) {
            if ($attempt->started_at) {
                $secondsLeft = $this->psyFlow->timeLeftSec($attempt, $test);
                $startedAtMs = $attempt->started_at->valueOf();
            } else {
                $sessionKey = "psy.{$test->getKey()}.started_at";
                if (!Session::has($sessionKey)) {
                    Session::put($sessionKey, now()->toIso8601String());
                }

                $startedAt = Carbon::parse(Session::get($sessionKey));
                $elapsed = now()->diffInSeconds($startedAt);
                $secondsLeft = max(0, $timeLimitMin * 60 - $elapsed);
                $startedAtMs = $startedAt->valueOf();
            }

            if ($secondsLeft === 0) {
                return redirect()->route('app.psy.attempts.submit', $test->slug ?: $test->id);
            }
        }

        return view('app.psy_questions.show', [
            'test' => $test,
            'question' => $question,
            'prevId' => $progress['prevId'],
            'nextId' => $progress['nextId'],
            'timeLimitMin' => $timeLimitMin,
            'startedAtMs' => $startedAtMs,
            'secondsLeft' => $secondsLeft,
            'attempt' => $attempt,
            'selectedOptionId' => $answer?->option_id,
            'typedValue' => $answer?->value,
            'current' => $progress['current'],
            'total' => $progress['total'],
            'pct' => $progress['pct'],
            'answeredIds' => $progress['answeredIds'],
            'answeredCount' => $progress['answeredCount'],
        ]);
    }
}

