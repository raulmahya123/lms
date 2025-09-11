<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TestIq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestIqController extends Controller
{
    /** Tampilkan soal test untuk user */
    public function show(TestIq $testIq)
    {
        abort_unless($testIq->is_active, 404);

        // (Opsional) Batasi 1 kali submit per user
        $userId = Auth::id();
        $alreadySubmitted = collect($testIq->submissions ?? [])
            ->contains(fn($s) => ($s['user_id'] ?? null) === $userId);

        if ($alreadySubmitted) {
            return redirect()->route('user.test-iq.result', $testIq);
        }

        return view('app.test_iq.show', [
            'test' => $testIq,
        ]);
    }

    /** Simpan jawaban user */
    public function submit(Request $r, TestIq $testIq)
    {
        abort_unless($testIq->is_active, 404);

        $userId = Auth::id();
        abort_unless($userId, 403);

        $data = $r->validate([
            'answers'      => ['required','array'],
            'answers.*'    => ['nullable','string'],
            'duration_sec' => ['nullable','integer'],
        ]);

        // hitung skor
        $questions = $testIq->questions ?? [];
        $correct = 0;
        foreach ($questions as $q) {
            $qid = $q['id'] ?? null;
            $ans = $q['answer'] ?? null;
            if ($qid && isset($data['answers'][$qid]) && $data['answers'][$qid] === $ans) {
                $correct++;
            }
        }
        $score = $correct;

        // simpan ke submissions JSON
        $subs = $testIq->submissions ?? [];
        $subs[] = [
            'user_id'      => $userId,
            'score'        => $score,
            'answers'      => $data['answers'],
            'duration_sec' => $data['duration_sec'] ?? null,
            'submitted_at' => now()->toIso8601String(),
        ];
        $testIq->submissions = $subs;
        $testIq->save();

        return redirect()->route('user.test-iq.result', $testIq)
            ->with('status', "Jawaban berhasil dikirim. Skor: {$score}/".count($questions));
    }

    /** Tampilkan hasil user */
    public function result(TestIq $testIq)
    {
        $userId = Auth::id();
        abort_unless($userId, 403);

        $subs = collect($testIq->submissions ?? [])->where('user_id',$userId)->values();
        $last = $subs->isEmpty() ? null : $subs->last();

        return view('app.test_iq.result', [
            'test'   => $testIq,
            'result' => $last,
        ]);
    }
}
