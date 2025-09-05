<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\QuizSubmitRequest;
use App\Models\{Lesson, Quiz, QuizAttempt, Answer};
use App\Models\CertificateIssue;
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

        // Cek kalau ada attempt aktif (belum submit), pakai itu; kalau tidak ada, buat attempt baru.
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
     * Submit attempt kuis.
     * - Hitung skor (tetap dihitung jika kamu butuh tampilkan angka)
     * - Hitung persen benar berbasis jumlah soal MCQ
     * - Simpan jawaban
     * - Update attempt (submitted_at)
     * - Auto-terbit certificate issue jika >= 80% benar
     */
    public function submit(QuizSubmitRequest $r, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('id', $r->attempt_id)
            ->where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        abort_if($attempt->submitted_at, 422, 'Attempt sudah disubmit.');

        // Muat semua yang diperlukan untuk penilaian dan akses course
        $quiz->load('questions.options', 'lesson.module.course');
        $questions = $quiz->questions;

        $score = 0;
        $correctCount = 0;         // jumlah jawaban benar (hanya mcq)
        $totalGradable = 0;        // jumlah soal yang dinilai otomatis (mcq)

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
                    // Essay/open text → tidak dihitung dalam persentase otomatis
                    $text = trim((string) $input);
                    $isCorrect = false;
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

        // Hitung % benar (berbasis MCQ)
        $percent = $totalGradable > 0 ? ($correctCount / $totalGradable) * 100 : 0;

        // === Auto-issue sertifikat bila >= 80% benar ===
        if ($percent >= 80) {
            $course = $quiz->lesson->module->course;

            CertificateIssue::firstOrCreate(
                [
                    'user_id'         => Auth::id(),
                    'course_id'       => $course->id ?? null,
                    'assessment_type' => 'course',
                    'assessment_id'   => $attempt->id,
                ],
                [
                    'template_id' => $course->certificate_template_id ?? 1, // fallback template id = 1
                    'serial'      => Str::upper(Str::random(12)),
                    'score'       => $score,    // simpan juga skor kalau ingin ditampilkan di sertifikat
                    'issued_at'   => now(),
                ]
            );
        }

        return redirect()
            ->route('app.quiz.result', $attempt)
            ->with('status', 'Jawaban terkirim.');
    }

    /**
     * Halaman hasil attempt kuis:
     * - Tampilkan eligibility berdasarkan 80% benar (MCQ)
     */
    public function result(QuizAttempt $attempt)
    {
        $attempt->load(['quiz.lesson.module.course', 'answers.question.options']);

        // Hitung % benar dari jawaban yang dapat dinilai otomatis (MCQ)
        $mcqAnswers       = $attempt->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
        $totalGradable    = $mcqAnswers->count();
        $correctCount     = $mcqAnswers->where('is_correct', true)->count();
        $percent          = $totalGradable > 0 ? ($correctCount / $totalGradable) * 100 : 0;

        $course   = $attempt->quiz->lesson->module->course;
        $eligible = $percent >= 80; // syarat 80% benar

        // Kalau mau, kamu bisa kirim $percent ke view juga untuk ditampilkan
        return view('app.quizzes.result', [
            'attempt'  => $attempt,
            'eligible' => $eligible,
            'course'   => $course,
            'percent'  => $percent,
            'correct'  => $correctCount,
            'total'    => $totalGradable,
        ]);
    }
}
