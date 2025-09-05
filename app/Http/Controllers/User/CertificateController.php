<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Course, Enrollment, QuizAttempt};
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Generate sertifikat kursus berbasis:
     * - Sudah enrolled & completed (opsional: cek semua lessons completed)
     * - Atau berdasar skor max attempt terakhir (opsional)
     */
    public function course(Course $course)
    {
        // Pastikan user punya akses (enrolled)
        $enrolled = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        abort_unless($enrolled, 403, 'Kamu belum terdaftar di kursus ini.');

        $user = Auth::user();

        // (Opsional) ambil skor terbaik kuis dalam course ini
        $bestAttempt = QuizAttempt::where('user_id', $user->id)
            ->whereHas('quiz.lesson.module.course', fn($q)=>$q->where('id',$course->id))
            ->orderByDesc('score')
            ->first();

        $data = [
            'user' => $user,
            'course' => $course,
            'bestAttempt' => $bestAttempt,
            'issued_at' => now(),
            'serial' => 'CERT-'.now()->format('Ymd').'-'.$user->id.'-'.$course->id,
        ];

        $pdf = Pdf::loadView('app.certificates.course', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'certificate-'.$course->id.'-'.$user->id.'.pdf';
        return $pdf->download($filename);
    }
}
