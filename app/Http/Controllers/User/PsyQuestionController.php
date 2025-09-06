<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PsyTest;
use App\Models\PsyQuestion;
use Illuminate\Http\Request;

class PsyQuestionController extends Controller
{
    /**
     * GET /psy-tests/{slugOrId}/questions/{question}
     * Tampilkan 1 soal dari test aktif dengan prev/next berdasarkan ordering.
     */
    public function show(Request $r, string|int $slugOrId, PsyQuestion $question)
    {
        // Cari test aktif (slug atau id)
        $test = PsyTest::query()
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('id', $slugOrId)->orWhere('slug', $slugOrId);
            })
            ->firstOrFail();

        // Pastikan soal milik test tsb
        abort_if($question->test_id !== $test->id, 404, 'Question not found in this test');

        // Load opsi dengan urutan
        $question->load(['options' => fn($q) => $q->orderBy('ordering')->orderBy('id')]);

        // Ambil daftar id soal berurutan untuk prev/next
        $siblings = $test->questions()
            ->orderBy('ordering')->orderBy('id')
            ->pluck('id')->all();

        $idx  = array_search($question->id, $siblings, true);
        $prev = ($idx !== false && $idx > 0) ? $siblings[$idx - 1] : null;
        $next = ($idx !== false && $idx < count($siblings) - 1) ? $siblings[$idx + 1] : null;

        return view('app.psy_questions.show', [
            'test'     => $test,
            'question' => $question,
            'prevId'   => $prev,
            'nextId'   => $next,
        ]);
    }
}
