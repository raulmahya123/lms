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
    // === KONSTANTA ATURAN LULUS/REMED & LIMIT ===
    private const MAX_ATTEMPTS      = 2;   // 1x normal + 1x remed
    private const COOLDOWN_SECONDS  = 10;  // 10 detik (aktif jika gagal setelah 2 attempt)
    private const PASS_MIN_PERCENT  = 80;  // minimal lulus
    private const REMED_MAX_PERCENT = 70;  // attempt-1 ≤75 => wajib remed

    /**
     * Mulai / lanjut attempt untuk quiz dari sebuah lesson.
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

        // (Opsional) cek enrollment utk course berbayar
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

        // === Batas attempt 2 total (submitted) ===
        $submittedCount = $quiz->attempts()
            ->where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->count();

        if ($submittedCount >= self::MAX_ATTEMPTS) {
            // Cek apakah SUDAH LULUS (untuk menentukan apakah perlu cooldown)
            $bestPctOnQuiz = $this->bestPercentByPointsOnQuiz(Auth::id(), $quiz->id);
            $hasPassed = $bestPctOnQuiz >= self::PASS_MIN_PERCENT;

            if (! $hasPassed) {
                // Kunci hanya jika GAGAL setelah 2 attempt
                $lastSubmittedAt = $quiz->attempts()
                    ->where('user_id', Auth::id())
                    ->whereNotNull('submitted_at')
                    ->latest('submitted_at')
                    ->value('submitted_at');

                if ($lastSubmittedAt) {
                    $elapsed = now()->diffInSeconds($lastSubmittedAt); // int
                    $remain  = max(0, self::COOLDOWN_SECONDS - $elapsed);
                    if ($remain > 0) {
                        return back()->withErrors([
                            'quiz' => "Batas 2 percobaan tercapai. Terkunci {$remain} detik.",
                        ]);
                    }
                }
            }

            // >>> Di sini logika/teks "Anda sudah lulus..." DIHAPUS <<< //
            // Pesan generik saja setelah 2 attempt, tanpa menyebut lulus tidak perlu mengulang
            return back()->withErrors([
                'quiz' => 'Batas 2 percobaan tercapai. Anda tidak dapat mencoba lagi.',
            ]);
        }

        // Pakai attempt aktif jika ada; kalau tidak, buat baru
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
     * Submit attempt kuis.
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
            // Hapus jawaban lama untuk question_id yang dikirim (idempotent)
            $incomingQids = $questions->pluck('id')
                ->intersect(collect($r->input('answers', []))->keys()->map(fn($k) => (int)$k));

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
                        $belongs = $q->options->contains('id', $optionId);
                        if (! $belongs) {
                            $optionId = null; // skip option asing
                        }
                    }

                    $correct = $q->options->firstWhere('is_correct', 1);
                    $isCorrect = $correct && $correct->id === $optionId;
                } else {
                    // Essay/short answer (tidak auto-grade)
                    $text = is_string($input) ? trim($input) : null;
                    $isCorrect = false;
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

        // === Persentase lulus berbasis poin MCQ ===
        $maxPointsMcq = $quiz->questions
            ->where('type', 'mcq')
            ->sum(fn($q) => $q->points ?? 1);

        $percent = $maxPointsMcq > 0 ? ($score / $maxPointsMcq) * 100 : 0;

        // Tentukan nomor attempt (1 = pertama, 2 = remed)
        $priorSubmitted = $quiz->attempts()
            ->where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->where('id', '!=', $attempt->id)
            ->count();

        $attemptNo = $priorSubmitted + 1;

        // === Keputusan kelulusan ===
        $passed = $percent >= self::PASS_MIN_PERCENT;

        // === Upsert certificate issue bila lulus ===
        if ($passed) {
            $course = $quiz->lesson->module->course;
            $templateId = $course->certificate_template_id ?? 1;

            $issue = CertificateIssue::firstOrCreate(
                [
                    'user_id'         => Auth::id(),
                    'course_id'       => $course->id ?? null,
                    'assessment_type' => 'course',
                ],
                [
                    'template_id' => $templateId,
                    'serial'      => $this->generateUniqueSerial(),
                    'issued_at'   => now(),
                ]
            );

            $issue->update([
                'assessment_id' => $attempt->id,
                'score'         => $score,
                'issued_at'     => now(),
            ]);

            return redirect()
                ->route('app.quiz.result', $attempt)
                ->with('status', "Lulus — Nilai: ".number_format($percent,1)."%. Sertifikat tersedia.");
        }

        // Tidak lulus (<80)
        if ($attemptNo === 1) {
            // Attempt pertama: info remed
            $msg = "Belum lulus (".number_format($percent,1)."%). ";
            if ($percent <= self::REMED_MAX_PERCENT) {
                $msg .= "Nilai ≤".self::REMED_MAX_PERCENT." — wajib remed (kesempatan terakhir).";
            } else {
                $msg .= "Minimal lulus ".self::PASS_MIN_PERCENT."%. Anda masih punya 1 kesempatan remed.";
            }
            return redirect()
                ->route('app.quiz.result', $attempt)
                ->with('status', $msg);
        }

        // Attempt ke-2 (remed): final — tetap tidak lulus bila <80
        return redirect()
            ->route('app.quiz.result', $attempt)
            ->with('status', "Tidak lulus (percobaan ke-2) — Nilai: ".number_format($percent,1)."%. Minimal ".self::PASS_MIN_PERCENT."%.");
    }

    /**
     * Halaman hasil attempt.
     */
    public function result(QuizAttempt $attempt): View
    {
        abort_if($attempt->user_id !== Auth::id(), 403, 'Anda tidak berhak melihat hasil ini.');

        $attempt->load(['quiz.lesson.module.course', 'answers.question.options']);
        $course = $attempt->quiz->lesson->module->course;

        // % untuk attempt ini (by points)
        [$percentCurrent, $correctCurrent, $totalCurrent] = $this->percentForAttempt($attempt);

        // attempt terbaik (masih count-based)
        [$bestAttempt, $bestPercent, $bestCorrect, $bestTotal] = $this->bestAttemptOnCourse(
            userId: $attempt->user_id,
            courseId: $course->id
        );

        // Banner data (tanpa logika "sudah lulus tidak perlu mengulang")
        $submittedCount = $attempt->quiz->attempts()
            ->where('user_id', $attempt->user_id)
            ->whereNotNull('submitted_at')
            ->count();

        $remainAttempts = max(0, self::MAX_ATTEMPTS - $submittedCount);

        // Cooldown hanya bila sudah 2 attempt & BELUM lulus (aturan awal tetap dipertahankan)
        $bestPctOnQuiz = $this->bestPercentByPointsOnQuiz($attempt->user_id, $attempt->quiz_id);
        $hasPassed = $bestPctOnQuiz >= self::PASS_MIN_PERCENT;

        $cooldownRemain = 0;
        if ($submittedCount >= self::MAX_ATTEMPTS && ! $hasPassed) {
            $lastSubmittedAt = $attempt->quiz->attempts()
                ->where('user_id', $attempt->user_id)
                ->whereNotNull('submitted_at')
                ->latest('submitted_at')
                ->value('submitted_at');

            if ($lastSubmittedAt) {
                $elapsed = now()->diffInSeconds($lastSubmittedAt);
                $cooldownRemain = max(0, self::COOLDOWN_SECONDS - $elapsed);
            }
        }

        return view('app.quizzes.result', [
            'attempt'         => $attempt,
            'course'          => $course,

            // attempt ini
            'percent'         => $percentCurrent,
            'correct'         => $correctCurrent,
            'total'           => $totalCurrent,
            'eligible'        => $percentCurrent >= self::PASS_MIN_PERCENT,

            // attempt terbaik
            'bestAttempt'     => $bestAttempt,
            'best_percent'    => $bestPercent,
            'best_correct'    => $bestCorrect,
            'best_total'      => $bestTotal,
            'eligible_best'   => $bestPercent >= self::PASS_MIN_PERCENT,

            // banner
            'maxAttempts'     => self::MAX_ATTEMPTS,
            'cooldownSeconds' => self::COOLDOWN_SECONDS,
            'submittedCount'  => $submittedCount,
            'remainAttempts'  => $remainAttempts,
            'cooldownRemain'  => $cooldownRemain,
            // NOTE: variabel $hasPassed tetap dipass jika view kamu masih memerlukannya
            'hasPassed'       => $hasPassed,
        ]);
    }

    /**
     * Hitung % benar MCQ (berbasis poin) untuk satu attempt.
     * @return array [percent, correctCountMCQ, totalMcq]
     */
    private function percentForAttempt(QuizAttempt $attempt): array
    {
        $mcq = $attempt->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
        $total   = $mcq->count();
        $correct = $mcq->where('is_correct', true)->count();

        $maxPoints = $attempt->quiz->questions
            ->where('type','mcq')
            ->sum(fn($q) => $q->points ?? 1);

        $scorePoints = 0;
        foreach ($mcq as $ans) {
            if ($ans->is_correct && $ans->question) {
                $scorePoints += ($ans->question->points ?? 1);
            }
        }

        $percent = $maxPoints > 0 ? ($scorePoints / $maxPoints) * 100 : 0;
        return [$percent, $correct, $total];
    }

    /**
     * Ambil attempt TERBAIK (berdasar persentase MCQ benar).
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
     * Persentase terbaik (by points) milik user pada kuis tertentu.
     */
    private function bestPercentByPointsOnQuiz(int $userId, int $quizId): float
    {
        $attempts = QuizAttempt::with(['answers.question', 'quiz'])
            ->where('user_id', $userId)
            ->where('quiz_id', $quizId)
            ->whereNotNull('submitted_at')
            ->get();

        $best = 0.0;
        foreach ($attempts as $att) {
            $mcq = $att->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');

            $maxPoints = $att->quiz->questions()
                ->where('type', 'mcq')
                ->get()
                ->sum(fn($q) => $q->points ?? 1);

            $scorePoints = 0;
            foreach ($mcq as $ans) {
                if ($ans->is_correct && $ans->question) {
                    $scorePoints += ($ans->question->points ?? 1);
                }
            }

            $pct = $maxPoints > 0 ? ($scorePoints / $maxPoints) * 100 : 0;
            if ($pct > $best) {
                $best = $pct;
            }
        }
        return $best;
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

    /**
     * Buat serial unik untuk sertifikat (sekali saja).
     */
    private function generateUniqueSerial(): string
    {
        do {
            $s = Str::upper(Str::random(12));
        } while (CertificateIssue::where('serial', $s)->exists());
        return $s;
    }
}
