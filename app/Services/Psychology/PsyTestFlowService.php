<?php

namespace App\Services\Psychology;

use App\Models\PsyAnswer;
use App\Models\PsyAttempt;
use App\Models\PsyProfile;
use App\Models\PsyTest;
use Illuminate\Support\Facades\DB;

class PsyTestFlowService
{
    public function resolveActiveTest(string $slugOrId, array $columns = ['id', 'name', 'slug', 'track', 'type', 'is_active', 'time_limit_min', 'created_at']): PsyTest
    {
        if (!in_array('id', $columns, true)) {
            $columns[] = 'id';
        }

        return PsyTest::query()
            ->select($columns)
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('id', $slugOrId)->orWhere('slug', $slugOrId);
            })
            ->firstOrFail();
    }

    public function activeAttempt(string $userId, string $testId): ?PsyAttempt
    {
        return PsyAttempt::query()
            ->where('user_id', $userId)
            ->where('test_id', $testId)
            ->whereNull('submitted_at')
            ->first();
    }

    public function startOrResumeAttempt(string $userId, string $testId): PsyAttempt
    {
        return PsyAttempt::firstOrCreate(
            ['user_id' => $userId, 'test_id' => $testId, 'submitted_at' => null],
            ['started_at' => now()]
        );
    }

    public function timeLeftSec(?PsyAttempt $attempt, PsyTest $test): ?int
    {
        $limitMin = (int) ($test->time_limit_min ?? 0);
        if (!$attempt || $limitMin <= 0 || !$attempt->started_at) {
            return null;
        }

        $elapsed = now()->diffInSeconds($attempt->started_at);
        return max(0, ($limitMin * 60) - $elapsed);
    }

    /**
     * @return list<string>
     */
    public function orderedQuestionIds(PsyTest $test): array
    {
        return $test->questions()
            ->orderBy('ordering')
            ->orderBy('created_at')
            ->pluck('id')
            ->all();
    }

    /**
     * @return list<string>
     */
    public function answeredQuestionIds(PsyAttempt $attempt): array
    {
        return PsyAnswer::query()
            ->where('attempt_id', $attempt->id)
            ->pluck('question_id')
            ->all();
    }

    public function nextUnansweredQuestionId(PsyTest $test, PsyAttempt $attempt): ?string
    {
        $questionIds = $this->orderedQuestionIds($test);
        $answeredIds = $this->answeredQuestionIds($attempt);

        return collect($questionIds)
            ->first(fn ($id) => !in_array($id, $answeredIds, true))
            ?? ($questionIds[0] ?? null);
    }

    public function nextQuestionId(PsyTest $test, string $currentQuestionId): ?string
    {
        $questionIds = $this->orderedQuestionIds($test);
        $idx = array_search($currentQuestionId, $questionIds, true);

        return ($idx !== false && $idx < count($questionIds) - 1)
            ? $questionIds[$idx + 1]
            : null;
    }

    public function questionProgress(PsyTest $test, PsyAttempt $attempt, ?string $currentQuestionId = null): array
    {
        $questionIds = $this->orderedQuestionIds($test);
        $idx = $currentQuestionId ? array_search($currentQuestionId, $questionIds, true) : false;
        $answeredIds = $this->answeredQuestionIds($attempt);
        $total = count($questionIds);
        $answeredCount = count($answeredIds);

        return [
            'questionIds' => $questionIds,
            'answeredIds' => $answeredIds,
            'answeredCount' => $answeredCount,
            'total' => $total,
            'current' => $idx === false ? 1 : ($idx + 1),
            'prevId' => ($idx !== false && $idx > 0) ? $questionIds[$idx - 1] : null,
            'nextId' => ($idx !== false && $idx < $total - 1) ? $questionIds[$idx + 1] : null,
            'pct' => $total > 0 ? (int) floor(($answeredCount / $total) * 100) : 0,
        ];
    }

    public function profileForScore(string $testId, int $total): ?PsyProfile
    {
        $profile = PsyProfile::query()
            ->where('test_id', $testId)
            ->where('min_total', '<=', $total)
            ->where(function ($q) use ($total) {
                $q->whereNull('max_total')->orWhere('max_total', '>=', $total);
            })
            ->orderByDesc('min_total')
            ->first();

        if ($profile) {
            return $profile;
        }

        $profile = PsyProfile::query()
            ->where('test_id', $testId)
            ->where('min_total', '<=', $total)
            ->orderByDesc('min_total')
            ->first();

        return $profile ?: PsyProfile::query()
            ->where('test_id', $testId)
            ->where('max_total', '>=', $total)
            ->orderBy('max_total')
            ->first();
    }

    public function profileRange(string $testId): array
    {
        return [
            'min' => PsyProfile::where('test_id', $testId)->min('min_total'),
            'max' => PsyProfile::where('test_id', $testId)->max('max_total'),
        ];
    }

    public function finalizeAttempt(PsyAttempt $attempt, PsyTest $test): PsyAttempt
    {
        return DB::transaction(function () use ($attempt, $test) {
            $attempt->loadMissing([
                'answers' => fn ($q) => $q->select(['id', 'attempt_id', 'question_id', 'option_id', 'value']),
                'answers.question:id,test_id,trait_key',
                'answers.option:id,value',
            ]);

            $traitTotals = [];
            $traitCounts = [];
            $total = 0;

            foreach ($attempt->answers as $answer) {
                if (!$answer->question || $answer->question->test_id !== $test->id) {
                    continue;
                }

                $trait = $answer->question->trait_key ?: 'general';
                $value = (int) ($answer->score ?? (!is_null($answer->value) ? $answer->value : ($answer->option->value ?? 0)));

                $traitTotals[$trait] = ($traitTotals[$trait] ?? 0) + $value;
                $traitCounts[$trait] = ($traitCounts[$trait] ?? 0) + 1;
                $total += $value;
            }

            $scores = [];
            foreach ($traitTotals as $trait => $sum) {
                $scores[$trait] = round($sum / max(1, $traitCounts[$trait]), 2);
            }
            $scores['_total'] = $total;

            $profile = $this->profileForScore($test->id, $total);

            $attempt->score_json = $scores;
            $attempt->total_score = $total;
            $attempt->result_key = $profile?->key;
            $attempt->recommendation_text = $profile
                ? trim($profile->name . ($profile->description ? ' - ' . $profile->description : ''))
                : 'Profil belum terdefinisi untuk rentang skor ini.';
            $attempt->submitted_at = now();
            $attempt->save();

            return $attempt;
        });
    }
}

