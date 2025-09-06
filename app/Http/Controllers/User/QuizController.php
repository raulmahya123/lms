<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\QuizSubmitRequest;
use App\Models\{Lesson, Quiz, QuizAttempt, Answer, CertificateIssue};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    /**
     * Mulai / lanjut attempt untuk quiz dari sebuah lesson.
     */
    public function start(Lesson $lesson)
    {
        $lesson->load('quiz.questions.options','module.course');
        $quiz = $lesson->quiz;
        abort_if(!$quiz, 404, 'Quiz tidak tersedia');

        // hitung attempt yg sudah disubmit
        $submittedCount = $quiz->attempts()
            ->where('user_id', auth()->id())
            ->whereNotNull('submitted_at')
            ->count();

        // batas attempt (null = unlimited)
        if (!is_null($quiz->max_attempts) && $submittedCount >= $quiz->max_attempts) {
            return back()->with('status', "Batas percobaan tercapai (max {$quiz->max_attempts}x).");
        }

        // pakai attempt aktif jika ada; kalau tidak, buat baru
        $attempt = $quiz->attempts()
            ->where('user_id', auth()->id())
            ->whereNull('submitted_at')
            ->first();

        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'quiz_id'      => $quiz->id,
                'user_id'      => auth()->id(),
                'score'        => 0,
                'started_at'   => now(),
                'submitted_at' => null,
            ]);
        }

        return view('app.quizzes.take', compact('lesson','quiz','attempt'));
    }

    /**
     * Submit attempt kuis:
     * - Simpan jawaban
     * - Hitung skor & % benar MCQ
     * - Tandai submitted
     * - Upsert certificate issue (satu per user-course) jika >= 80%
     */
    public function submit(QuizSubmitRequest $r, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('id', $r->attempt_id)
            ->where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        abort_if($attempt->submitted_at, 422, 'Attempt sudah disubmit.');

        // muat untuk penilaian
        $quiz->load('questions.options','lesson.module.course');
        $questions = $quiz->questions;

        $score = 0;
        $correctCount = 0;   // benar MCQ
        $totalGradable = 0;  // total MCQ

        DB::transaction(function () use ($r, $questions, &$score, &$correctCount, &$totalGradable, $attempt) {
            foreach ($questions as $q) {
                $input     = $r->input("answers.{$q->id}");
                $isCorrect = false;
                $optionId  = null;
                $text      = null;

                if ($q->type === 'mcq') {
                    $totalGradable++;
                    $optionId = (int) $input;
                    $correct  = $q->options()->where('is_correct', 1)->first();
                    $isCorrect = $correct && $correct->id === $optionId;
                } else {
                    $text = trim((string) $input);
                    $isCorrect = false; // essay tak dinilai otomatis
                }

                if ($isCorrect) {
                    $correctCount++;
                    $score += (int) $q->points;
                }

                Answer::create([
                    'attempt_id'  => $attempt->id,
                    'question_id' => $q->id,
                    'option_id'   => $optionId,
                    'text_answer' => $text,
                    'is_correct'  => $isCorrect,
                ]);
            }

            $attempt->update([
                'score'        => $score,
                'submitted_at' => now(),
            ]);
        });

        // % benar MCQ utk attempt ini
        $percent = $totalGradable > 0 ? ($correctCount / $totalGradable) * 100 : 0;

        // === Upsert certificate issue (satu per user-course) bila >= 80% ===
        if ($percent >= 80) {
            $course = $quiz->lesson->module->course;

            // kunci unik: user_id + course_id + assessment_type
            CertificateIssue::updateOrCreate(
                [
                    'user_id'         => Auth::id(),
                    'course_id'       => $course->id ?? null,
                    'assessment_type' => 'course',
                ],
                [
                    'assessment_id' => $attempt->id, // simpan attempt yang meluluskan/terbaru
                    'template_id'   => $course->certificate_template_id ?? 1,
                    'serial'        => Str::upper(Str::random(12)),
                    'score'         => $score,
                    'issued_at'     => now(),
                ]
            );
        }

        return redirect()
            ->route('app.quiz.result', $attempt)
            ->with('status', 'Jawaban terkirim.');
    }

    /**
     * Halaman hasil attempt:
     * - Tampilkan % benar attempt ini (eligible_current)
     * - Hitung juga attempt TERBAIK user di course yang sama (eligible_best)
     */
    public function result(QuizAttempt $attempt)
    {
        $attempt->load(['quiz.lesson.module.course', 'answers.question.options']);
        $course = $attempt->quiz->lesson->module->course;

        // % untuk attempt ini
        [$percentCurrent, $correctCurrent, $totalCurrent] = $this->percentForAttempt($attempt);

        // cari attempt TERBAIK user pada course yang sama
        [$bestAttempt, $bestPercent, $bestCorrect, $bestTotal] = $this->bestAttemptOnCourse(
            userId: $attempt->user_id,
            courseId: $course->id
        );

        return view('app.quizzes.result', [
            'attempt'         => $attempt,
            'course'          => $course,

            // attempt ini
            'percent'         => $percentCurrent,
            'correct'         => $correctCurrent,
            'total'           => $totalCurrent,
            'eligible'        => $percentCurrent >= 80, // kompatibel dgn view lama

            // attempt terbaik (pakai ini utk tombol “Unduh Sertifikat” agar konsisten dgn download)
            'bestAttempt'     => $bestAttempt,
            'best_percent'    => $bestPercent,
            'best_correct'    => $bestCorrect,
            'best_total'      => $bestTotal,
            'eligible_best'   => $bestPercent >= 80,
        ]);
    }

    /**
     * Hitung % benar MCQ untuk satu attempt.
     * @return array [percent, correct, total]
     */
    private function percentForAttempt(QuizAttempt $attempt): array
    {
        $mcq = $attempt->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
        $total   = $mcq->count();
        $correct = $mcq->where('is_correct', true)->count();
        $percent = $total > 0 ? ($correct / $total) * 100 : 0;
        return [$percent, $correct, $total];
    }

    /**
     * Ambil attempt TERBAIK (persentase MCQ tertinggi) milik user pada course.
     * @return array [QuizAttempt|null $bestAttempt, float $percent, int $correct, int $total]
     */
    private function bestAttemptOnCourse(int $userId, int $courseId): array
    {
        $attempts = QuizAttempt::with(['answers.question','quiz.lesson.module.course'])
            ->where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->whereHas('quiz.lesson.module.course', fn($q) => $q->where('id', $courseId))
            ->get();

        $bestAttempt = null;
        $bestPercent = 0.0;
        $bestCorrect = 0;
        $bestTotal   = 0;

        foreach ($attempts as $att) {
            $mcq = $att->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
            $total   = $mcq->count();
            $correct = $mcq->where('is_correct', true)->count();
            $pct     = $total > 0 ? ($correct / $total) * 100 : 0;

            if ($pct > $bestPercent) {
                $bestAttempt = $att;
                $bestPercent = $pct;
                $bestCorrect = $correct;
                $bestTotal   = $total;
            }
        }

        return [$bestAttempt, $bestPercent, $bestCorrect, $bestTotal];
    }
}
