<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Enrollment, QuizAttempt, CertificateIssue, CertificateTemplate};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * Generate/unduh sertifikat course:
     * - Pastikan user enrolled
     * - Pastikan eligible (>=80% benar dari MCQ di course ini)
     * - Catat ke certificate_issues (sinkron dgn Admin)
     * - Render & simpan PDF ke storage, update pdf_path
     */
    public function course(Course $course)
    {
        // 1) Enrolled check
        $enrolled = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        abort_unless($enrolled, 403, 'Kamu belum terdaftar di kursus ini.');

        $user = Auth::user();

        // 2) Eligibility: 80% benar (hitung dari attempt yang SUBMITTED di course ini)
        [$percent, $correct, $total] = $this->computePercentCorrectForCourse($user->id, $course->id);
        abort_unless($total > 0 && $percent >= 80, 403, 'Belum memenuhi syarat (>= 80% benar).');

        // Ambil attempt dengan skor tertinggi (opsional, buat ditampilkan)
        $bestAttempt = QuizAttempt::where('user_id', $user->id)
            ->whereHas('quiz.lesson.module.course', fn($q) => $q->where('id', $course->id))
            ->whereNotNull('submitted_at')
            ->orderByDesc('score')
            ->first();

        // 3) Pilih template aktif (prioritas: yang tertaut ke course -> fallback: template aktif pertama -> id=1)
        $templateId = $course->certificate_template_id
            ?? CertificateTemplate::where('is_active', true)->value('id')
            ?? 1;

        // 4) Catat/ambil issue agar Admin melihat datanya
        $issue = CertificateIssue::firstOrCreate(
            [
                'user_id'         => $user->id,
                'course_id'       => $course->id,
                'assessment_type' => 'course',
                // Simpan assessment_id sebagai attempt terbaik jika ada
                'assessment_id'   => optional($bestAttempt)->id,
            ],
            [
                'template_id' => $templateId,
                'serial'      => $this->makeSerial($user->id, $course->id),
                'score'       => optional($bestAttempt)->score ?? 0,
                'issued_at'   => now(),
            ]
        );

        // 5) Render PDF & simpan file (kalau belum ada atau mau regen)
        $data = [
            'user'        => $user,
            'course'      => $course,
            'bestAttempt' => $bestAttempt,
            'issued_at'   => $issue->issued_at,
            'serial'      => $issue->serial,
            'percent'     => round($percent, 2),
            'correct'     => $correct,
            'total'       => $total,
            // Kalau perlu, kirim juga $issue->template untuk style background, dll.
            'template'    => CertificateTemplate::find($templateId),
        ];

        $pdf = Pdf::loadView('app.certificates.course', $data)
            ->setPaper('a4', 'landscape');

        // Path simpan (public storage agar bisa diakses admin)
        $dir      = "certificates/{$course->id}";
        $filename = "certificate-{$course->id}-user-{$user->id}.pdf";
        $path     = "{$dir}/{$filename}";

        // Pastikan direktori ada
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        // Simpan file & update path bila kosong/berbeda
        Storage::disk('public')->put($path, $pdf->output());
        if ($issue->pdf_path !== $path) {
            $issue->update(['pdf_path' => $path]);
        }

        // 6) Download ke user (kalau mau stream pakai ->stream($filename))
        return response()->download(
            storage_path("app/public/{$path}"),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Hitung persentase benar (MCQ) untuk seluruh quiz di course.
     * Hanya hitung attempt yang submitted.
     * @return array [percent, correctCount, totalGradable]
     */
    private function computePercentCorrectForCourse(int $userId, int $courseId): array
    {
        // Ambil SEMUA jawaban dari attempts user pada course ini
        $attempts = QuizAttempt::with(['answers.question', 'quiz.lesson.module.course'])
            ->where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->whereHas('quiz.lesson.module.course', fn($q) => $q->where('id', $courseId))
            ->get();

        $correct = 0;
        $total   = 0;

        foreach ($attempts as $attempt) {
            foreach ($attempt->answers as $ans) {
                if ($ans->question && $ans->question->type === 'mcq') {
                    $total++;
                    if ($ans->is_correct) $correct++;
                }
            }
        }

        $percent = $total > 0 ? ($correct / $total) * 100 : 0;
        return [$percent, $correct, $total];
    }

    /**
     * Format serial unik untuk sertifikat.
     */
    private function makeSerial(int $userId, int $courseId): string
    {
        return 'CERT-' . now()->format('Ymd') . '-' . Str::padLeft((string)$userId, 5, '0') . '-' . Str::padLeft((string)$courseId, 5, '0');
    }
}
