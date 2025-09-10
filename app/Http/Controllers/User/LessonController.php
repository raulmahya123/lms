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
            // whitelist drive (hanya field yang diperlukan)
            'driveWhitelists' => fn($q) => $q->select(['id','lesson_id','user_id','email','status','verified_at']),
        ]);

        $user   = Auth::user();
        $course = $lesson->module->course;

        // --- konten & link ---
        $blocks = $this->toArray($lesson->content);
        $links  = $this->toArray($lesson->content_url); // [{title,url,type}, ...]

        // --- satu link Google Drive (untuk panel akses) ---
        $rawDriveLink = $lesson->drive_link ?: collect($links)
            ->pluck('url')
            ->filter()
            ->first(fn($u) => str_contains((string)$u, 'drive.google.com'));

        // --- whitelist drive (SINGLE SOURCE OF TRUTH) ---
        $wls  = $lesson->driveWhitelists;
        $myWl = $wls->firstWhere('user_id', $user->id)
             ?? $wls->firstWhere('email', $user->email);
        $myStatus = $myWl->status ?? 'none';

        $summary = [
            'approved' => $wls->where('status','approved')->count(),
            'pending'  => $wls->where('status','pending')->count(),
            'rejected' => $wls->where('status','rejected')->count(),
            'total'    => $wls->count(),
        ];

        // 🚫 Expose link only if approved
        $driveLink = ($myStatus === 'approved') ? $rawDriveLink : null;

        // Tidak ada global status; hanya info per-user & ringkasan
        $drive = [
            'link'         => $driveLink,
            'my_whitelist' => [
                'status'      => $myStatus,            // approved|pending|rejected|none
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

        // --- progress user saat ini ---
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

        LessonProgress::updateOrCreate(
            ['lesson_id' => $lesson->id, 'user_id' => Auth::id()],
            $payload + [
                'lesson_id' => $lesson->id,
                'user_id'   => Auth::id(),
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
