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
    Enrollment,
    QuizSeasonLock   // <= MODEL BARU
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class QuizController extends Controller
{
    // === ATURAN ===
    private const MAX_ATTEMPTS_PER_SEASON = 2;    // 2x per season
    private const SEASON_SECONDS          = 86400; // contoh: harian (ganti sesuai kebutuhan)
    private const PASS_MIN_PERCENT        = 80;   // minimal lulus
    private const REMED_MAX_PERCENT       = 70;   // attempt-1 ≤70 => wajib remed

    /**
     * Mulai / lanjut attempt
     */
    public function start(Lesson $lesson): RedirectResponse|View
    {
        $lesson->load(['quiz.questions.options', 'module.course']);
        $quiz = $lesson->quiz;
        abort_if(!$quiz, 404, 'Quiz tidak tersedia');

        // Wajib lesson selesai
        if (!$this->isLessonCompleted($lesson, Auth::id())) {
            return back()->withErrors(['quiz' => 'Selesaikan pelajaran ini terlebih dahulu sebelum memulai kuis.']);
        }

        // (Opsional) cek enrollment utk course berbayar
        if (method_exists($lesson->module->course, 'isPaid') && $lesson->module->course->isPaid()) {
            $isEnrolled = Enrollment::query()
                ->where('user_id', Auth::id())
                ->where('course_id', $lesson->module->course->id)
                ->exists();
            if (!$isEnrolled) {
                return back()->withErrors(['quiz' => 'Anda belum terdaftar pada kelas ini.']);
            }
        }

        // === LIMIT 2x PER SEASON via quiz_season_locks ===
        [$seasonStart, $seasonEnd, $seasonKey] = $this->currentSeason();

        /** @var QuizSeasonLock $lock */
        $lock = QuizSeasonLock::firstOrCreate(
            [
                'user_id'    => Auth::id(),
                'quiz_id'    => $quiz->id,
                'season_key' => $seasonKey,
            ],
            [
                'season_start'   => $seasonStart,
                'season_end'     => $seasonEnd,
                'attempt_count'  => 0,
                'last_attempt_at'=> null,
            ]
        );

        if ((int)$lock->attempt_count >= self::MAX_ATTEMPTS_PER_SEASON) {
            $end  = $lock->season_end instanceof Carbon ? $lock->season_end : Carbon::parse($lock->season_end);
            $remain = now()->lt($end) ? now()->diffInSeconds($end) : 0;
            return back()->withErrors([
                'quiz' => "Batas ".self::MAX_ATTEMPTS_PER_SEASON." percobaan per season tercapai. Musim baru dalam {$remain} detik.",
            ]);
        }

        // Pakai attempt aktif jika ada; kalau tidak, buat baru
        $attempt = $quiz->attempts()
            ->where('user_id', Auth::id())
            ->whereNull('submitted_at')
            ->first();

        if (!$attempt) {
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
     * Submit attempt
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
        $correctCount = 0;
        $totalGradable = 0;

        DB::transaction(function () use ($r, $questions, &$score, &$correctCount, &$totalGradable, $attempt, $quiz) {

            // === Hitung skor & simpan jawaban (idempotent) ===
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
                    $optionId = is_null($input) ? null : (int)$input;
                    if ($optionId && !$q->options->contains('id', $optionId)) {
                        $optionId = null; // opsi asing
                    }
                    $correct   = $q->options->firstWhere('is_correct', 1);
                    $isCorrect = $correct && $correct->id === $optionId;
                } else {
                    $text = is_string($input) ? trim($input) : null; // essay
                    $isCorrect = false;
                }

                if ($isCorrect) {
                    $correctCount++;
                    $score += (int)($q->points ?? 1);
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

            // === INCREMENT COUNTER di quiz_season_locks (satu-satunya tempat hitung attempt per season) ===
            [$seasonStart, $seasonEnd, $seasonKey] = $this->currentSeason();

            /** @var QuizSeasonLock $lock */
            $lock = QuizSeasonLock::lockForUpdate()->firstOrCreate(
                [
                    'user_id'    => $attempt->user_id,
                    'quiz_id'    => $quiz->id,
                    'season_key' => $seasonKey,
                ],
                [
                    'season_start'   => $seasonStart,
                    'season_end'     => $seasonEnd,
                    'attempt_count'  => 0,
                    'last_attempt_at'=> null,
                ]
            );

            // Guard: bila sudah penuh (race), jangan lebih dari 2
            if ((int)$lock->attempt_count >= self::MAX_ATTEMPTS_PER_SEASON) {
                // Batalkan submit? Atau biarkan submit tapi tanpa menaikkan counter?
                // Kita tolak dengan exception agar konsisten.
                abort(422, 'Batas percobaan per season tercapai.');
            }

            $lock->increment('attempt_count');
            $lock->update([
                'last_attempt_at' => now(),
            ]);
        });

        // Persentase berbasis poin
        $maxPointsMcq = $quiz->questions->where('type', 'mcq')->sum(fn($q) => $q->points ?? 1);
        $percent = $maxPointsMcq > 0 ? ($score / $maxPointsMcq) * 100 : 0;

        // Ambil nomor attempt dalam season (berdasarkan lock terkini)
        [$seasonStart, $seasonEnd, $seasonKey] = $this->currentSeason();
        $lock = QuizSeasonLock::where([
            'user_id'    => Auth::id(),
            'quiz_id'    => $quiz->id,
            'season_key' => $seasonKey,
        ])->first();
        $attemptNo = $lock ? (int)$lock->attempt_count : 1;

        // Lulus?
        $passed = $percent >= self::PASS_MIN_PERCENT;

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
                ->with('quiz_status', "Lulus — Nilai: ".number_format($percent,1)."%. Sertifikat tersedia.");
        }

        // Tidak lulus
        if ($attemptNo === 1) {
            $msg = "Belum lulus (".number_format($percent,1)."%). ";
            if ($percent <= self::REMED_MAX_PERCENT) {
                $msg .= "Nilai ≤".self::REMED_MAX_PERCENT." — wajib remed (kesempatan terakhir season ini).";
            } else {
                $msg .= "Minimal lulus ".self::PASS_MIN_PERCENT."%. Masih ada 1 kesempatan di season ini.";
            }
            return redirect()->route('app.quiz.result', $attempt)->with('quiz_status', $msg);
        }

        return redirect()
            ->route('app.quiz.result', $attempt)
            ->with('quiz_status', "Tidak lulus (percobaan ke-{$attemptNo} dalam season ini) — Nilai: ".number_format($percent,1)."%. Minimal ".self::PASS_MIN_PERCENT."%.");
    }

    /**
     * Halaman hasil
     */
    public function result(QuizAttempt $attempt): View
    {
        abort_if($attempt->user_id !== Auth::id(), 403, 'Anda tidak berhak melihat hasil ini.');

        $attempt->load(['quiz.lesson.module.course', 'answers.question.options']);
        $course = $attempt->quiz->lesson->module->course;

        // % untuk attempt ini (by points)
        [$percentCurrent, $correctCurrent, $totalCurrent] = $this->percentForAttempt($attempt);

        // Attempt terbaik (count-based)
        [$bestAttempt, $bestPercent, $bestCorrect, $bestTotal] = $this->bestAttemptOnCourse(
            userId: $attempt->user_id,
            courseId: $course->id
        );

        // === Data per-season dari quiz_season_locks ===
        [$seasonStart, $seasonEnd, $seasonKey] = $this->currentSeason();
        $lock = QuizSeasonLock::where([
            'user_id'    => $attempt->user_id,
            'quiz_id'    => $attempt->quiz_id,
            'season_key' => $seasonKey,
        ])->first();

        $submittedInSeason = $lock ? (int)$lock->attempt_count : 0;
        $remainAttempts    = max(0, self::MAX_ATTEMPTS_PER_SEASON - $submittedInSeason);
        $end               = $lock && $lock->season_end ? Carbon::parse($lock->season_end) : $seasonEnd;
        $seasonRemain      = now()->lt($end) ? now()->diffInSeconds($end) : 0;

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

            // banner / per-season
            'maxAttempts'     => self::MAX_ATTEMPTS_PER_SEASON,
            'submittedCount'  => $submittedInSeason,
            'remainAttempts'  => $remainAttempts,
            'seasonRemain'    => $seasonRemain, // detik sampai season berakhir
        ]);
    }

    /**
     * Hitung % benar MCQ (by points)
     */
    private function percentForAttempt(QuizAttempt $attempt): array
    {
        $mcq = $attempt->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
        $total   = $mcq->count();
        $correct = $mcq->where('is_correct', true)->count();

        $maxPoints = $attempt->quiz->questions->where('type','mcq')->sum(fn($q) => $q->points ?? 1);

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
     * Attempt terbaik (count-based)
     */
    private function bestAttemptOnCourse(int $userId, int $courseId): array
    {
        $attempts = QuizAttempt::with(['answers.question', 'quiz.lesson.module.course'])
            ->where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->whereHas('quiz.lesson.module.course', fn($q) => $q->where('id', $courseId))
            ->get();

        $bestAttempt = null; $bestPercent = 0.0; $bestCorrect = 0; $bestTotal = 0;

        foreach ($attempts as $att) {
            $mcq = $att->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
            $total   = $mcq->count();
            $correct = $mcq->where('is_correct', true)->count();
            $pct     = $total > 0 ? ($correct / $total) * 100 : 0;

            if ($pct > $bestPercent) {
                $bestAttempt = $att; $bestPercent = $pct; $bestCorrect = $correct; $bestTotal = $total;
            }
        }
        return [$bestAttempt, $bestPercent, $bestCorrect, $bestTotal];
    }

    /**
     * Dapatkan [season_start, season_end, season_key]
     */
    private function currentSeason(): array
    {
        $nowTs = now()->timestamp;
        $startTs = intdiv($nowTs, self::SEASON_SECONDS) * self::SEASON_SECONDS;
        $seasonStart = Carbon::createFromTimestamp($startTs, now()->timezoneName);
        $seasonEnd   = $seasonStart->copy()->addSeconds(self::SEASON_SECONDS);
        $seasonKey   = (string)$seasonStart->timestamp; // kunci stabil per durasi
        return [$seasonStart, $seasonEnd, $seasonKey];
    }

    private function isLessonCompleted(Lesson $lesson, int $userId): bool
    {
        return LessonProgress::query()
            ->where('lesson_id', $lesson->id)
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->exists();
    }

    private function generateUniqueSerial(): string
    {
        do { $s = Str::upper(Str::random(12)); } while (CertificateIssue::where('serial', $s)->exists());
        return $s;
    }
}
