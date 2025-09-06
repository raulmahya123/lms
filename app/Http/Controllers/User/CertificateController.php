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
        [$bestAttempt, $percent, $correct, $total] = $this->bestAttemptEligibilityForCourse($user->id, $course->id);
        abort_unless($total > 0, 403, 'Belum ada soal MCQ yang bisa dinilai.');
        abort_unless($percent >= 80, 403, "Belum memenuhi syarat ({$correct}/{$total} = ".round($percent,2)."%).");

        // 3) Template sertifikat
        $templateId = $course->certificate_template_id
            ?? CertificateTemplate::where('is_active', true)->value('id')
            ?? 1;

        // 4) Catat issue (supaya admin bisa lihat)
        CertificateIssue::firstOrCreate(
            [
                'user_id'         => $user->id,
                'course_id'       => $course->id,
                'assessment_type' => 'course',
                'assessment_id'   => optional($bestAttempt)->id,
            ],
            [
                'template_id' => $templateId,
                'serial'      => $this->makeSerial($user->id, $course->id),
                'score'       => optional($bestAttempt)->score ?? 0,
                'issued_at'   => now(),
            ]
        );

        // 5) Render PDF langsung (tanpa simpan)
        $data = [
            'user'        => $user,
            'course'      => $course,
            'bestAttempt' => $bestAttempt,
            'issued_at'   => now(),
            'serial'      => $this->makeSerial($user->id, $course->id),
            'percent'     => round($percent, 2),
            'correct'     => $correct,
            'total'       => $total,
            'template'    => CertificateTemplate::find($templateId),
        ];

        $pdf = Pdf::loadView('app.certificates.course', $data)->setPaper('a4', 'landscape');

        // Langsung download tanpa save
        $filename = "certificate-{$course->id}-user-{$user->id}.pdf";
        return $pdf->download($filename);

        // Kalau mau tampil di browser:
        // return $pdf->stream($filename);
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
