<?php

namespace App\Services;

use App\Models\PsyProfile;
use App\Models\PsyTest;
use App\Models\User;

class PsyAccess
{
    public static function canAccess(User $user, PsyTest $test): bool
    {
        // Sesuaikan dengan logika membership/plan milikmu.
        // Contoh default: jika tes premium, butuh membership aktif.
        if (!($test->is_premium ?? false)) {
            return true;
        }

        $m = $user->memberships()->active()->first();
        return (bool) $m;
    }

    /**
     * Cari profil berdasarkan total skor.
     * Menerima PsyTest model atau test_id (UUID string / int).
     */
    public static function findProfile(PsyTest|int|string $test, int $total): ?PsyProfile
    {
        // Ambil primary key sebagai string (mendukung UUID)
        $testId = $test instanceof PsyTest ? (string) $test->getKey() : (string) $test;

        return PsyProfile::where('test_id', $testId)
            ->where('min_total', '<=', $total)
            ->where(function ($q) use ($total) {
                $q->whereNull('max_total')->orWhere('max_total', '>=', $total);
            })
            ->orderByDesc('min_total')
            ->first();
    }
}
