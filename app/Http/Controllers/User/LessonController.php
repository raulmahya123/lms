<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProgressRequest;
use App\Models\{Lesson, Enrollment, LessonProgress};
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        $lesson->load([
            'module.course',
            'quiz.questions.options',
            'resources',
            // penting untuk whitelist drive:
            'driveWhitelists' => fn($q) => $q->select(['id','lesson_id','user_id','email','status','verified_at']),
        ]);

        $user   = Auth::user();
        $course = $lesson->module->course;

        // --- konten & link ---
        $blocks = $this->toArray($lesson->content);
        $links  = $this->toArray($lesson->content_url); // [{title,url,type}, ...]

        // --- satu link Google Drive (untuk panel akses) ---
        $driveLink = collect($links)
            ->pluck('url')
            ->filter()
            ->first(fn($u) => str_contains((string)$u, 'drive.google.com'));

        // --- ringkasan whitelist drive ---
        $wls  = $lesson->driveWhitelists;
        $myWl = $wls->firstWhere('user_id', $user->id)
             ?? $wls->firstWhere('email', $user->email);

        $summary = [
            'approved' => $wls->where('status','approved')->count(),
            'pending'  => $wls->where('status','pending')->count(),
            'rejected' => $wls->where('status','rejected')->count(),
            'total'    => $wls->count(),
        ];

        $driveStatusGlobal = $lesson->drive_status ?? null;

        $drive = [
            'link'         => $driveLink,
            'status'       => $driveStatusGlobal, // pending/approved/rejected/null
            'my_whitelist' => [
                'status'      => $myWl->status ?? 'none',
                'verified_at' => $myWl->verified_at ?? null,
            ],
            'summary'      => $summary,
        ];

        // --- prev/next ---
        $siblings = $lesson->module->lessons()
            ->orderBy('ordering')->orderBy('id')
            ->pluck('id')->values();

        $idx  = $siblings->search($lesson->id);
        $prev = ($idx !== false && $idx > 0) ? $siblings[$idx - 1] : null;
        $next = ($idx !== false && $idx < $siblings->count() - 1) ? $siblings[$idx + 1] : null;

        // --- progress user saat ini (tanpa perlu helper di model) ---
        $progress = LessonProgress::query()
            ->where('lesson_id', $lesson->id)
            ->where('user_id', Auth::id())
            ->first();

        // --- resources tambahan ---
        $resources = $lesson->resources()->orderBy('id')->get();

        return view('app.lessons.show', compact(
            'lesson','course','blocks','links','resources','prev','next','progress','drive'
        ));
    }

    public function updateProgress(UpdateProgressRequest $r, Lesson $lesson)
    {
        $payload = [
            'progress' => $r->input('progress', []),
        ];

        if ($r->boolean('completed')) {
            $payload['completed_at'] = now();
        }

        // Tidak bergantung pada relasi; aman walau model Lesson belum punya progresses()
        LessonProgress::updateOrCreate(
            ['lesson_id' => $lesson->id, 'user_id' => Auth::id()],
            $payload + [
                'lesson_id' => $lesson->id,
                'user_id'   => Auth::id(),
                // tambahkan default watched jika perlu
                'watched'   => (bool)($r->input('progress.watched', true)),
            ]
        );

        return back()->with('status', 'Progress tersimpan.');
    }

    // ====================== Helpers ======================

    protected function toArray($value): array
    {
        if (is_array($value)) return $value;
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    protected function isDrive(?string $url): bool
    {
        return $url && str_contains($url, 'drive.google.com');
    }
}
