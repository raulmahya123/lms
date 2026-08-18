<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{PsyAttempt, PsyTest, PsyAnswer};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class PsyAttemptController extends Controller
{
    /**
     * List attempts + filter + search + paginate.
     */
    public function index(Request $r)
    {
        $filters = $r->validate([
            'test_id'   => ['nullable', 'uuid', 'exists:psy_tests,id'],
            'status'    => ['nullable', Rule::in(['submitted','in-progress'])],
            'q'         => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $term = trim((string) ($filters['q'] ?? ''));

        // dropdown filter: pakai kolom 'name'
        $tests = PsyTest::orderBy('name')->get(['id','name']);

        $attempts = PsyAttempt::query()
            ->select(['id', 'test_id', 'user_id', 'started_at', 'submitted_at', 'total_score', 'result_key', 'created_at'])
            ->with([
                'test:id,name',        // pakai name, bukan title
                'user:id,name,email',
            ])
            ->when($filters['test_id'] ?? null, fn (Builder $q, $testId) =>
                $q->where('test_id', $testId)
            )
            ->when($filters['status'] ?? null, function (Builder $q, $status) {
                return $status === 'submitted'
                    ? $q->whereNotNull('submitted_at')
                    : $q->whereNull('submitted_at');
            })
            ->when($term !== '', function (Builder $q) use ($term) {
                $q->where(function (Builder $sub) use ($term) {
                    $sub->whereHas('user', function (Builder $u) use ($term) {
                            $u->where('name','like',"%{$term}%")
                              ->orWhere('email','like',"%{$term}%");
                        })
                        ->orWhere('id', $term)
                        ->orWhere('result_key','like',"%{$term}%");
                });
            })
            ->when($filters['date_from'] ?? null, fn (Builder $q, $from) =>
                $q->whereDate('started_at','>=', $from)
            )
            ->when($filters['date_to'] ?? null, fn (Builder $q, $to) =>
                $q->whereDate('started_at','<=', $to)
            )
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.psy_attempts.index', compact('attempts','tests'));
    }

    /**
     * Detail + jawaban.
     */
    public function show(PsyAttempt $psy_attempt)
    {
        $psy_attempt->load([
            'test:id,name',
            'user:id,name,email',
            'answers' => fn ($q) => $q->select(['id', 'attempt_id', 'question_id', 'option_id', 'value'])->orderBy('id'),
            // ⬇️ ganti 'text' -> 'prompt'; pilih kolom yang memang ada
            'answers.question:id,test_id,ordering,prompt',
            // kalau tabel psy_options tidak ada 'weight', jangan dipilih
            'answers.option:id,question_id,label,value,ordering',
        ]);

        $durationSeconds = null;
        if ($psy_attempt->started_at) {
            $end = $psy_attempt->submitted_at ?: now();
            $durationSeconds = $end->diffInSeconds($psy_attempt->started_at);
        }

        return view('admin.psy_attempts.show', [
            'attempt'         => $psy_attempt,
            'durationSeconds' => $durationSeconds,
        ]);
    }

    /**
     * Hapus attempt + jawabannya.
     */
    public function destroy(PsyAttempt $psy_attempt)
    {
        PsyAnswer::where('attempt_id', $psy_attempt->id)->delete();
        $psy_attempt->delete();

        return redirect()
            ->route('admin.psy-attempts.index')
            ->with('ok', 'Attempt dihapus.');
    }
}
