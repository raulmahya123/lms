<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{PsyAttempt, PsyTest, PsyAnswer};
use Illuminate\Database\Eloquent\Builder;

class PsyAttemptController extends Controller
{
    /**
     * List attempts + filter + search + paginate.
     */
    public function index(Request $r)
    {
        // filter dropdown: pakai kolom 'name'
        $tests = PsyTest::orderBy('name')->get(['id','name']);

        $attempts = PsyAttempt::query()
            ->with([
                'test:id,name',        // <-- pakai name, bukan title
                'user:id,name,email',
            ])
            ->when($r->filled('test_id'), fn (Builder $q) =>
                $q->where('test_id', (int)$r->input('test_id'))
            )
            ->when($r->filled('status') && in_array($r->status, ['submitted','in-progress'], true), function (Builder $q) use ($r) {
                $r->status === 'submitted'
                    ? $q->whereNotNull('submitted_at')
                    : $q->whereNull('submitted_at');
            })
            ->when($r->filled('q'), function (Builder $q) use ($r) {
                $term = trim((string)$r->q);
                $q->where(function (Builder $sub) use ($term) {
                    $sub->whereHas('user', function (Builder $u) use ($term) {
                            $u->where('name','like',"%{$term}%")
                              ->orWhere('email','like',"%{$term}%");
                        })
                        ->orWhere('id', $term)
                        ->orWhere('result_key','like',"%{$term}%");
                });
            })
            ->when($r->filled('date_from'), fn (Builder $q) =>
                $q->whereDate('started_at','>=', $r->date('date_from'))
            )
            ->when($r->filled('date_to'), fn (Builder $q) =>
                $q->whereDate('started_at','<=', $r->date('date_to'))
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
            'test:id,name',          // <-- pakai name
            'user:id,name,email',
            'answers' => fn ($q) => $q->orderBy('id'),
            'answers.question:id,test_id,ordering,text',
            'answers.option:id,question_id,label,value,weight',
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
            ->with('ok','Attempt dihapus.');
    }
}
