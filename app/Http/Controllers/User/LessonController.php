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
            'driveWhitelists' => fn($q) => $q->select(['id', 'lesson_id', 'user_id', 'email', 'status', 'verified_at']),
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
            'approved' => $wls->where('status', 'approved')->count(),
            'pending'  => $wls->where('status', 'pending')->count(),
            'rejected' => $wls->where('status', 'rejected')->count(),
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
            'lesson',
            'course',
            'blocks',
            'links',
            'resources',
            'prev',
            'next',
            'progress',
            'drive'
        ));
    }

    public function updateProgress(UpdateProgressRequest $r, Lesson $lesson)
    {
        $userId   = Auth::id();
        $existing = LessonProgress::where('lesson_id', $lesson->id)
            ->where('user_id', $userId)
            ->first();

        $old = $existing?->progress ?? [];
        $oldItems = (array) data_get($old, 'items', []);

        // Daftar semua key item yang ada di form (dibuat di Blade)
        $allKeysCsv = (string) $r->input('all_keys', '');
        $allKeys    = array_values(array_filter(array_map('trim', explode(',', $allKeysCsv))));

        // Checkbox yang dicentang: progress[items][<key>] = "1"
        $checked = array_keys((array) $r->input('progress.items', [])); // hanya yang dicentang masuk request

        // Bangun map item baru (false untuk yang tidak dicentang)
        $newItems = $oldItems;
        foreach ($allKeys as $k) {
            $newItems[$k] = in_array($k, $checked, true);
        }

        // Completed manual lewat checkbox "completed"
        $completed = $r->boolean('completed');

        LessonProgress::updateOrCreate(
            ['lesson_id' => $lesson->id, 'user_id' => $userId],
            [
                'lesson_id'    => $lesson->id,
                'user_id'      => $userId,
                'progress'     => array_replace($old, ['items' => $newItems]),
                'completed_at' => $completed ? now() : null, // boleh uncomplete
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
    public function requestDriveAccess(Lesson $lesson)
    {
        $user  = Auth::user();
        $email = $user->email;

        // Cari entri whitelist existing utk user ini (prioritas by user_id, fallback by email)
        $existing = $lesson->driveWhitelists()
            ->where(function ($q) use ($user, $email) {
                $q->where('user_id', $user->id)->orWhere('email', $email);
            })
            ->first();

        // Jika sudah approved, jangan ditimpa
        if ($existing && $existing->status === 'approved') {
            return back()->with('status', 'Akses Drive kamu sudah disetujui.');
        }

        // Upsert ke status pending
        $lesson->driveWhitelists()->updateOrCreate(
            [
                'user_id'   => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'email'       => $email,
                'status'      => 'pending',
                'verified_at' => null, // reset verifikasi saat ajukan ulang
            ]
        );

        return back()->with('status', 'Permintaan akses dikirim. Status: pending.');
    }
}
