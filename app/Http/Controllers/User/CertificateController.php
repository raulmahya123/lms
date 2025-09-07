<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{
    Course, Enrollment, QuizAttempt,
    CertificateIssue, CertificateTemplate
};
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    // === EXISTING ===
    public function course(Course $course)
    {
        $user = Auth::user();

        // 1) Pastikan enrolled
        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        abort_unless($enrolled, 403, 'Kamu belum terdaftar/aktif di kursus ini.');

        // 2) Ambil attempt terbaik (>=80% MCQ benar)
        [$bestAttempt, $percent, $correct, $total] =
            $this->bestAttemptEligibilityForCourse($user->id, $course->id);

        abort_unless($total > 0, 403, 'Belum ada soal MCQ yang bisa dinilai.');
        abort_unless($percent >= 80, 403, "Belum memenuhi syarat ({$correct}/{$total} = ".round($percent,2)."%).");

        // 3) Template sertifikat
        $templateId = $course->certificate_template_id
            ?? CertificateTemplate::where('is_active', true)->value('id')
            ?? 1;

        // 4) Catat issue
        $issue = CertificateIssue::firstOrCreate(
            [
                'user_id'         => $user->id,
                'course_id'       => $course->id,
                'assessment_type' => 'course',
                'assessment_id'   => optional($bestAttempt)->id,
            ],
            [
                'template_id' => $templateId,
                'serial'      => $this->makeSerial($user->id, $course->id),
                'score'       => optional($bestAttempt)->score ?? round($percent, 2),
                'issued_at'   => now(),
            ]
        );

        // 5) Render PDF langsung (download)
        return $this->renderPdfForIssue($issue, download: true);
    }

    // === NEW: list sertifikat user (tabel) ===
    public function index()
    {
        $user = Auth::user();

        $issues = CertificateIssue::with(['course','template'])
            ->where('user_id', $user->id)
            ->orderByDesc('issued_at')
            ->paginate(12);

        return view('app.certificates.index', compact('issues'));
    }

    // === NEW: detail sertifikat (halaman dengan info + tombol preview/download) ===
    public function show(CertificateIssue $issue)
    {
        $this->authorizeIssue($issue);

        $issue->load(['course','template']);
        return view('app.certificates.show', compact('issue'));
    }

    // === NEW: preview stream di browser ===
    public function preview(CertificateIssue $issue)
    {
        $this->authorizeIssue($issue);
        return $this->renderPdfForIssue($issue, download: false);
    }

    // === NEW: download file ===
    public function download(CertificateIssue $issue)
    {
        $this->authorizeIssue($issue);
        return $this->renderPdfForIssue($issue, download: true);
    }

    // ===== Helpers =====

    private function authorizeIssue(CertificateIssue $issue): void
    {
        abort_unless($issue->user_id === Auth::id(), 403);
    }

    /**
     * Render PDF untuk sebuah CertificateIssue (preview stream / download).
     */
    private function renderPdfForIssue(CertificateIssue $issue, bool $download = false)
    {
        $user   = Auth::user();
        $course = $issue->course ?? Course::find($issue->course_id);

        // hitung percent/correct/total jika bisa, fallback ke score yang tersimpan
        $percent = is_numeric($issue->score) ? floatval($issue->score) : 0.0;
        $correct = null;
        $total   = null;
        $bestAttempt = null;

        if ($issue->assessment_type === 'course') {
            // Coba recompute dari attempt terbaik (opsional)
            [$bestAttempt, $pct, $corr, $tot] =
                $this->bestAttemptEligibilityForCourse($issue->user_id, $issue->course_id);
            if ($tot > 0) {
                $percent = round($pct, 2);
                $correct = $corr;
                $total   = $tot;
            }
        } elseif ($issue->assessment_type === 'quiz' && $issue->assessment_id) {
            $bestAttempt = QuizAttempt::with(['answers.question','quiz.lesson.module.course'])
                ->where('id', $issue->assessment_id)->first();
            if ($bestAttempt) {
                $mcq     = $bestAttempt->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
                $total   = $mcq->count();
                $correct = $mcq->where('is_correct', true)->count();
                $percent = $total > 0 ? round(($correct / $total) * 100, 2) : (float)$percent;
            }
        }

        $template = $issue->template ?? CertificateTemplate::find($issue->template_id);

        $data = [
            'user'        => $user,
            'course'      => $course,
            'bestAttempt' => $bestAttempt,
            'issued_at'   => $issue->issued_at ?? now(),
            'serial'      => $issue->serial,
            'percent'     => $percent,
            'correct'     => $correct,
            'total'       => $total,
            'template'    => $template,
            'issue'       => $issue,
        ];

        $pdf = Pdf::loadView('app.certificates.course', $data)
            ->setPaper('a4', 'landscape');

        $filename = "certificate-{$issue->id}.pdf";
        return $download ? $pdf->download($filename) : $pdf->stream($filename);
    }

    private function bestAttemptEligibilityForCourse(int $userId, int $courseId): array
    {
        $attempts = QuizAttempt::with(['answers.question', 'quiz.lesson.module.course'])
            ->where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->whereHas('quiz.lesson.module.course', fn($q) => $q->where('id', $courseId))
            ->get();

        $bestAttempt = null;
        $bestPercent = 0;
        $bestCorrect = 0;
        $bestTotal   = 0;

        foreach ($attempts as $attempt) {
            $mcq     = $attempt->answers->filter(fn($a) => $a->question && $a->question->type === 'mcq');
            $total   = $mcq->count();
            $correct = $mcq->where('is_correct', true)->count();
            $pct     = $total > 0 ? ($correct / $total) * 100 : 0;

            if ($pct > $bestPercent) {
                $bestAttempt = $attempt;
                $bestPercent = $pct;
                $bestCorrect = $correct;
                $bestTotal   = $total;
            }
        }

        return [$bestAttempt, $bestPercent, $bestCorrect, $bestTotal];
    }

    private function makeSerial(int $userId, int $courseId): string
    {
        return 'CERT-'
            . now()->format('Ymd') . '-'
            . Str::padLeft((string)$userId, 5, '0') . '-'
            . Str::padLeft((string)$courseId, 5, '0');
    }
}
