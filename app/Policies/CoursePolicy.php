<?php

namespace App\Policies;

use App\Models\{Course, User};

class CoursePolicy
{
    /**
     * Admin boleh semua.
     */
    public function before(User $user, string $ability)
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        // semua user login boleh lihat daftar (atau ubah sesuai kebutuhan)
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        // publik bila published; kalau tidak, creator/mentor boleh
        if ($course->is_published) return true;
        if (!$user) return false;

        return $course->created_by === $user->id
            || $course->mentors()->whereKey($user->id)->exists();
    }

    public function create(User $user): bool
    {
        // admin/mentor boleh buat course
        return method_exists($user, 'isMentor') && $user->isMentor();
    }

    public function manage(User $user, Course $course): bool
    {
        // creator atau mentor yang ditugaskan
        return $course->created_by === $user->id
            || $course->mentors()->whereKey($user->id)->exists();
    }

    public function update(User $user, Course $course): bool
    {
        return $this->manage($user, $course);
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->manage($user, $course);
    }

    // contoh ability khusus (opsional): assign mentor, ubah publish, dll.
    public function assignMentor(User $user, Course $course): bool
    {
        return $this->manage($user, $course);
    }
}
