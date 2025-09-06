<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\QuizSubmitRequest;
use App\Models\{
    Lesson,
    Quiz,
    QuizAttempt,
    Answer,
    CertificateIssue,
    LessonProgress,
    Enrollment
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Mulai / lanjut attempt untuk quiz dari sebuah lesson.
     * - Wajib: user sudah menandai lesson sebagai selesai (completed_at != null)
     * - Hormati max_attempts (hanya menghitung attempt yg sudah submitted)
     */
    public function start(Lesson $lesson): RedirectResponse|View
    {
        $lesson->load(['quiz.questions.options', 'module.course']);
        $quiz = $lesson->quiz;
        abort_if(!$quiz, 404, 'Quiz tidak tersedia');

        // --- Wajib lesson selesai dulu ---
        if (! $this->isLessonCompleted($lesson, Auth::id())) {
            return back()->withErrors([
                'quiz' => 'Selesaikan pelajaran ini terlebih dahulu sebelum memulai kuis.',
            ]);
        }

        // (Opsional tapi baik): pastikan user memang punya akses ke course (enrolled jika kursus berbayar)
        if (method_exists($lesson->module->course, 'isPaid') && $lesson->module->course->isPaid()) {
            $isEnrolled = Enrollment::query()
                ->where('user_id', Auth::id())
                ->where('course_id', $lesson->module->course->id)
                ->exists();

            if (! $isEnrolled) {
                return back()->withErrors([
                    'quiz' => 'Anda belum terdaftar pada kelas ini.',
                ]);
            }
        }

        // hitung attempt yg sudah disubmit
        $submittedCount = $quiz->attempts()
            ->where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->count();

        // batas attempt (null = unlimited)
        if (!is_null($quiz->max_attempts) && $submittedCount >= $quiz->max_attempts) {
            return back()->with('status', "Batas percobaan tercapai (maksimum {$quiz->max_attempts}x).");
        }

        // pakai attempt aktif jika ada; kalau tidak, buat baru
        $attempt = $quiz->attempts()
            ->where('user_id', Auth::id())
            ->whereNull('submitted_at')
            ->first();

        if (! $attempt) {
            $attempt = QuizAttempt::create([
                'quiz_id'      => $quiz->id,
                'user_id'      => Auth::id(),
                'score'        => 0,
                'started_at'   => now(),
                'submitted_at' => null,
            ]);
        }

        return view('app.quizzes.take', compact('lesson', 'quiz', 'attempt'));
    }

    /**
     * Submit attempt kuis:
     * - Validasi kepemilikan attempt
     * - Simpan jawaban (idempotent: hapus/replace jawaban lama di attempt ini untuk pertanyaan yang sama)
     * - Hitung skor & % benar MCQ
     * - Tandai submitted
     * - Upsert certificate issue (satu per user-course) jika >= 80%
     */
    public function submit(QuizSubmitRequest $r, Quiz $quiz): RedirectResponse
    {
        $attempt = QuizAttempt::query()
            ->where('id', $r->attempt_id)
            ->where('quiz_id', $quiz->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        abort_if($attempt->submitted_at, 422, 'Attempt sudah disubmit.');

        // muat untuk penilaian
        $quiz->load(['questions.options', 'lesson.module.course']);
        $questions = $quiz->questions;

        $score = 0;
        $correctCount = 0;   // jumlah MCQ benar
        $totalGradable = 0;  // jumlah MCQ

        DB::transaction(function () use ($r, $questions, &$score, &$correctCount, &$totalGradable, $attempt) {
            // Hapus jawaban lama untuk question_id yang dia kirim (idempotent)
            $incomingQids = collect($questions)->map->id->intersect(
                collect($r->input('answers', []))->keys()->map(fn($k) => (int)$k)
            );

            if ($incomingQids->isNotEmpty()) {
                Answer::where('attempt_id', $attempt->id)
                    ->whereIn('question_id', $incomingQids)
                    ->delete();
            }

            foreach ($questions as $q) {
                $input = $r->input("answers.{$q->id}", null);
                $isCorrect = false;
                $optionId  = null;
                $text      = null;

                if ($q->type === 'mcq') {
                    $totalGradable++;

                    // Validasi: option harus milik pertanyaan ini
                    $optionId = is_null($input) ? null : (int) $input;
                    if ($optionId) {
                        $belongs = $q->options()->where('id', $optionId)->exists();
                        if (! $belongs) {
                            // skip option asing (tidak menghitung apa pun)
                            $optionId = null;
                        }
                    }

                    $correct = $q->options->firstWhere('is_correct', 1);
                    $isCorrect = $correct && $correct->id === $optionId;
                } else {
                    // Essay/short answer
                    $text = is_string($input) ? trim($input) : null;
                    $isCorrect = false; // essay tidak dinilai otomatis
                }

                if ($isCorrect) {
                    $correctCount++;
                    $score += (int) ($q->points ?? 1); // fallback 1 poin jika null
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
            $templateId = $course->certificate_template_id ?? 1;

            // buat serial unik (cek tabrakan singkat)
            $serial = null;
            do {
                $serialTry = Str::upper(Str::random(12));
                $exists = CertificateIssue::where('serial', $serialTry)->exists();
                if (! $exists) $serial = $serialTry;
            } while (is_null($serial));

            CertificateIssue::updateOrCreate(
                [
                    'user_id'         => Auth::id(),
                    'course_id'       => $course->id ?? null,
                    'assessment_type' => 'course',
                ],
                [
                    'assessment_id' => $attempt->id, // simpan attempt pelulus TERBARU
                    'template_id'   => $templateId,
                    'serial'        => $serial,
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
    public function result(QuizAttempt $attempt): View
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
        $attempts = QuizAttempt::with(['answers.question', 'quiz.lesson.module.course'])
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

    /**
     * Cek apakah lesson sudah ditandai selesai oleh user.
     */
    private function isLessonCompleted(Lesson $lesson, int $userId): bool
    {
        return LessonProgress::query()
            ->where('lesson_id', $lesson->id)
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->exists();
    }
}
