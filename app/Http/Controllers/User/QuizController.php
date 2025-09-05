<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\QuizSubmitRequest;
use App\Models\{Lesson, Quiz, QuizAttempt, Answer, Enrollment};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function start(Lesson $lesson)
{
    $lesson->load('quiz.questions.options','module.course');
    $quiz = $lesson->quiz;
    abort_if(!$quiz, 404, 'Quiz tidak tersedia');

    // Hitung attempt yang SUDAH submit
    $submittedCount = $quiz->attempts()
        ->where('user_id', auth()->id())
        ->whereNotNull('submitted_at')
        ->count();

    // Batas attempt:
    //   - null → unlimited
    //   - angka → maksimal angka tsb
    if (!is_null($quiz->max_attempts) && $submittedCount >= $quiz->max_attempts) {
        return back()->with('status', "Batas percobaan tercapai (max {$quiz->max_attempts}x).");
    }

    // Cek kalau ada attempt aktif (belum submit), pakai itu;
    // kalau tidak ada, buat attempt baru.
    $attempt = $quiz->attempts()
        ->where('user_id', auth()->id())
        ->whereNull('submitted_at')
        ->first();

    if (!$attempt) {
        $attempt = QuizAttempt::create([
            'quiz_id'    => $quiz->id,
            'user_id'    => auth()->id(),
            'score'      => 0,
            'started_at' => now(),
            'submitted_at' => null,
        ]);
    }

    return view('app.quizzes.take', compact('lesson','quiz','attempt'));
}


    public function submit(QuizSubmitRequest $r, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('id',$r->attempt_id)
            ->where('quiz_id',$quiz->id)->where('user_id',Auth::id())->firstOrFail();
        abort_if($attempt->submitted_at, 422, 'Attempt sudah disubmit.');

        $quiz->load('questions.options');
        $questions = $quiz->questions;
        $score = 0;

        DB::transaction(function() use ($r, $questions, &$score, $attempt) {
            foreach ($questions as $q) {
                $input = $r->input("answers.{$q->id}");
                $isCorrect = false; $optionId = null; $text = null;

                if ($q->type === 'mcq') {
                    $optionId = (int)$input;
                    $correct = $q->options()->where('is_correct',1)->first();
                    $isCorrect = $correct && $correct->id === $optionId;
                } else {
                    $text = trim((string)$input);
                    $isCorrect = false;
                }

                if ($isCorrect) $score += (int)$q->points;

                Answer::create([
                    'attempt_id'=>$attempt->id,
                    'question_id'=>$q->id,
                    'option_id'=>$optionId,
                    'text_answer'=>$text,
                    'is_correct'=>$isCorrect,
                ]);
            }

            $attempt->update(['score'=>$score, 'submitted_at'=>now()]);
        });

        return redirect()->route('app.quiz.result',$attempt)->with('status','Jawaban terkirim.');
    }

    public function result(QuizAttempt $attempt)
    {
        $attempt->load(['quiz.lesson.module.course','answers.question.options']);
        return view('app.quizzes.result', compact('attempt'));
    }
}
