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
    CertificateTemplate,
    LessonProgress,
    Enrollment,
    QuizSeasonLock
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
    private const MAX_ATTEMPTS_PER_SEASON = 2;     // 2x per season
    private const SEASON_SECONDS          = 86400; // contoh: harian
    private const PASS_MIN_PERCENT        = 80;    // minimal lulus
    private const REMED_MAX_PERCENT       = 70;    // attempt-1 ≤70 => wajib remed

    /**
     * Mulai / lanjut attempt
     */
    public function start(Lesson $lesson): RedirectResponse|View
    {
        $lesson->load(['quiz.questions.options', 'module.course']);
        $quiz = $lesson->quiz;
        abort_if(!$quiz, 404, 'Quiz tidak tersedia');

        // 1) Harus selesai pelajaran
        if (!$this->isLessonCompleted($lesson, Auth::id())) {
            return back()->withErrors(['quiz' => 'Selesaikan pelajaran ini terlebih dahulu sebelum memulai kuis.']);
        }

        // 2) Enroll bila course berbayar
        $course = $lesson->module->course;
        if (data_get($course, 'is_paid')) {
            $isEnrolled = Enrollment::query()
                ->where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->exists();
            if (!$isEnrolled) {
                return back()->withErrors(['quiz' => 'Anda belum terdaftar pada kelas ini.']);
            }
        }

        // 3) Season lock
        [$seasonStart, $seasonEnd, $seasonKey] = $this->currentSeason();

        /** @var QuizSeasonLock $lock */
        $lock = QuizSeasonLock::firstOrCreate(
            [
                'user_id'    => Auth::id(),
                'quiz_id'    => $quiz->id,
                'season_key' => $seasonKey,
            ],
            [
                'season_start'     => $seasonStart,
                'season_end'       => $seasonEnd,
                'attempt_count'    => 0,
                'last_attempt_at'  => null,
            ]
        );

        // === Jika sudah mencapai batas → redirect ke hasil attempt terakhir (bukan back) ===
        if ((int) $lock->attempt_count >= self::MAX_ATTEMPTS_PER_SEASON) {
            $last = QuizAttempt::query()
                ->where('quiz_id', $quiz->id)
                ->where('user_id', Auth::id())
                ->whereNotNull('submitted_at')
                ->latest('submitted_at')
                ->first();

            if ($last) {
                return redirect()
                    ->route('app.quiz.result', $last) // -> /attempts/{uuid}
                    ->with('quiz_status', "Batas percobaan musim ini tercapai. Menampilkan hasil percobaan terakhir.");
            }

            // fallback kalau entah bagaimana tidak ada attempt tersubmit
            return redirect()
                ->route('app.lessons.show', $lesson)
                ->withErrors(['quiz' => 'Batas percobaan tercapai dan tidak ada riwayat yang bisa ditampilkan.']);
        }

        // 4) Lanjutkan attempt aktif jika ada
        $attempt = $quiz->attempts()
            ->where('user_id', Auth::id())
            ->whereNull('submitted_at')
            ->first();

        // 5) Atau buat attempt baru
        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'quiz_id'      => $quiz->id,
                'user_id'      => Auth::id(),
                'score'        => 0,
                'started_at'   => now(),
                'submitted_at' => null,
            ]);
        }

        // 6) Tampilkan halaman pengerjaan
        return view('app.quizzes.take', compact('lesson', 'quiz', 'attempt'));
    }


    /**
     * Submit attempt
     */
    public function submit(QuizSubmitRequest $r, Quiz $quiz): RedirectResponse
    {
        $attempt = QuizAttempt::query()
            ->where('id', $r->attempt_id) // UUID string
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

            // === Kumpulkan daftar question_id dari input TANPA cast ke int
            $incomingAnswers = (array) $r->input('answers', []);
            $incomingQids    = $questions->pluck('id')->intersect(array_keys($incomingAnswers));

            // idempotent: hapus jawaban lama untuk qid yang diinput kembali
            if ($incomingQids->isNotEmpty()) {
                Answer::where('attempt_id', $attempt->id)
                    ->whereIn('question_id', $incomingQids->values())
                    ->delete();
            }

            foreach ($questions as $q) {
                $input = $r->input("answers.{$q->id}", null);

                $isCorrect = false;
                $optionId  = null;
                $text      = null;

                if ($q->type === 'mcq') {
                    $totalGradable++;

                    // UUID string
                    $optionId = filled($input) ? (string) $input : null;

                    // Validasi opsi milik question ini
                    if ($optionId && ! $q->options->contains('id', $optionId)) {
                        $optionId = null;
                    }

                    $correctOption = $q->options->firstWhere('is_correct', 1);
                    $correctId     = $correctOption?->id;

                    // Bandingkan sebagai string aman
                    $isCorrect = $optionId && $correctId && hash_equals((string) $correctId, (string) $optionId);

                    if ($isCorrect) {
                        $correctCount++;
                        $score += (int) ($q->points ?? 1);
                    }
                } else {
                    // Essay / text
                    $text = is_string($input) ? trim($input) : null;
                    $isCorrect = false;
                }

                Answer::create([
                    'attempt_id'  => $attempt->id,
                    'question_id' => $q->id,
                    'option_id'   => $optionId,
                    'text_answer' => $text,
                    'is_correct'  => $isCorrect,
                ]);
            }

            // Simpan hasil attempt
            $attempt->update([
                'score'        => $score,
                'submitted_at' => now(),
            ]);

            // === Increment counter season (dengan lock)
            [$seasonStart, $seasonEnd, $seasonKey] = $this->currentSeason();

            /** @var QuizSeasonLock $lock */
            $lock = QuizSeasonLock::query()
                ->where([
                    'user_id'    => $attempt->user_id,
                    'quiz_id'    => $quiz->id,
                    'season_key' => $seasonKey,
                ])
                ->lockForUpdate()
                ->first();

            if (! $lock) {
                $lock = QuizSeasonLock::create([
                    'user_id'         => $attempt->user_id,
                    'quiz_id'         => $quiz->id,
                    'season_key'      => $seasonKey,
                    'season_start'    => $seasonStart,
                    'season_end'      => $seasonEnd,
                    'attempt_count'   => 0,
                    'last_attempt_at' => null,
                ]);
            }

            if ((int) $lock->attempt_count >= self::MAX_ATTEMPTS_PER_SEASON) {
                abort(422, 'Batas percobaan per season tercapai.');
            }

            $lock->increment('attempt_count');
            $lock->update([
                'last_attempt_at' => now(),
            ]);
        });

        // Persentase by points
        $maxPointsMcq = $quiz->questions->where('type', 'mcq')->sum(fn($q) => $q->points ?? 1);
        $percent = $maxPointsMcq > 0 ? ($score / $maxPointsMcq) * 100 : 0;

        // Ambil nomor attempt musim ini
        [$seasonStart, $seasonEnd, $seasonKey] = $this->currentSeason();
        $lock = QuizSeasonLock::where([
            'user_id'    => Auth::id(),
            'quiz_id'    => $quiz->id,
            'season_key' => $seasonKey,
        ])->first();
        $attemptNo = $lock ? (int) $lock->attempt_count : 1;

        // Lulus?
        $passed = $percent >= self::PASS_MIN_PERCENT;

        if ($passed) {
            $course = $quiz->lesson->module->course;

            // Selalu dapat template_id yang valid
            $templateId = $this->resolveCertificateTemplateId();

            // simpan/takeover satu issue per user+course+type
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
                'template_id'   => $templateId, // jaga-jaga jika pernah null
            ]);

            return redirect()
                ->route('app.quiz.result', $attempt)
                ->with('quiz_status', "Lulus — Nilai: " . number_format($percent, 1) . "%. Sertifikat tersedia.");
        }

        // Tidak lulus
        if ($attemptNo === 1) {
            $msg = "Belum lulus (" . number_format($percent, 1) . "%). ";
            if ($percent <= self::REMED_MAX_PERCENT) {
                $msg .= "Nilai ≤" . self::REMED_MAX_PERCENT . " — wajib remed (kesempatan terakhir season ini).";
            } else {
                $msg .= "Minimal lulus " . self::PASS_MIN_PERCENT . "%. Masih ada 1 kesempatan di season ini.";
            }
            return redirect()->route('app.quiz.result', $attempt)->with('quiz_status', $msg);
        }

        return redirect()
            ->route('app.quiz.result', $attempt)
            ->with('quiz_status', "Tidak lulus (percobaan ke-{$attemptNo} dalam season ini) — Nilai: " . number_format($percent, 1) . "%. Minimal " . self::PASS_MIN_PERCENT . "%.");
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

        // Attempt terbaik (by points) dalam course yang sama
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

        $submittedInSeason = $lock ? (int) $lock->attempt_count : 0;
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
        $mcq     = $attempt->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
        $total   = $mcq->count();
        $correct = $mcq->where('is_correct', true)->count();

        $maxPoints = $attempt->quiz->questions->where('type', 'mcq')->sum(fn($q) => $q->points ?? 1);

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
     * Attempt terbaik pada course (by points).
     * @return array{0: ?\App\Models\QuizAttempt, 1: float, 2: int, 3: int}
     */
    private function bestAttemptOnCourse(string $userId, string $courseId): array
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
            $mcq       = $att->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');

            $maxPoints = $att->quiz->questions
                ->where('type', 'mcq')
                ->sum(fn($q) => $q->points ?? 1);

            $scorePoints = 0;
            foreach ($mcq as $ans) {
                if ($ans->is_correct && $ans->question) {
                    $scorePoints += ($ans->question->points ?? 1);
                }
            }

            $pct = $maxPoints > 0 ? ($scorePoints / $maxPoints) * 100 : 0;

            if ($pct > $bestPercent) {
                $bestAttempt = $att;
                $bestPercent = $pct;
                $bestCorrect = $mcq->where('is_correct', true)->count();
                $bestTotal   = $mcq->count();
            }
        }

        return [$bestAttempt, $bestPercent, $bestCorrect, $bestTotal];
    }

    /**
     * Dapatkan [season_start, season_end, season_key]
     */
    private function currentSeason(): array
    {
        $nowTs      = now()->timestamp;
        $startTs    = intdiv($nowTs, self::SEASON_SECONDS) * self::SEASON_SECONDS;
        $seasonStart = Carbon::createFromTimestamp($startTs, now()->timezoneName);
        $seasonEnd  = $seasonStart->copy()->addSeconds(self::SEASON_SECONDS);
        $seasonKey  = (string) $seasonStart->timestamp; // kunci stabil per durasi
        return [$seasonStart, $seasonEnd, $seasonKey];
    }

    private function isLessonCompleted(Lesson $lesson, string $userId): bool
    {
        return LessonProgress::query()
            ->where('lesson_id', $lesson->id)
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->exists();
    }

    private function generateUniqueSerial(): string
    {
        do {
            $s = Str::upper(Str::random(12));
        } while (CertificateIssue::where('serial', $s)->exists());
        return $s;
    }

    /**
     * Pastikan selalu punya template sertifikat yang valid (non-null).
     */
    private function resolveCertificateTemplateId(): string
    {
        // 1) Cari template aktif
        $tpl = CertificateTemplate::where('is_active', true)->first();

        // 2) Kalau belum ada, buat default
        if (!$tpl) {
            $tpl = CertificateTemplate::create([
                'name'           => 'Default Certificate',
                'background_url' => '/storage/certificates/default.png',
                'fields_json'    => [],
                'svg_json'       => [],
                'is_active'      => true,
            ]);
        }

        return (string) $tpl->id;
    }
}
