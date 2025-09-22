<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Enrollment, Membership};

class EnsureCourseAccessible
{
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();

        // Ambil course dari parameter route (bisa 'course' atau dari 'lesson->module->course')
        $course = $request->route('course');
        if (!$course && ($lesson = $request->route('lesson'))) {
            $lesson->loadMissing('module.course');
            $course = $lesson->module?->course;
        }

        if (!$course) {
            return abort(404);
        }

        // Cek enrollment aktif
        $enr = Enrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if (!$enr) {
            return redirect()->route('app.courses.show', $course)
                ->with('info', 'Enroll dulu untuk mengakses course ini.');
        }

        // Cek membership saat ini
        $hasActiveMembership = Membership::where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        // Evaluasi hak akses efektif
        if (!$enr->hasEffectiveAccess($hasActiveMembership)) {
            return redirect()->route('app.memberships.plans')
                ->with('info', 'Membership tidak aktif / telah habis. Perpanjang untuk membuka course ini lagi.');
        }

        return $next($request);
    }
}
