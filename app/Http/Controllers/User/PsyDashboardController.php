<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PsyAttempt;
use App\Models\PsyTest;
use App\Models\PsyProfile;
use Illuminate\Support\Facades\Auth;

class PsyDashboardController extends Controller
{
    /**
     * Invokable controller untuk /app/psychology
     */
    public function __invoke()
    {
        $uid = Auth::id();

        // === Riwayat attempt (paginate) ===
        $attempts = PsyAttempt::with(['test:id,name,slug'])
            ->where('user_id', $uid)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->paginate(10);

        // Normalisasi judul untuk Blade (title dari name)
        $attempts->getCollection()->transform(function ($a) {
            if ($a->relationLoaded('test') && $a->test) {
                $a->test->title = $a->test->name;
            }
            return $a;
        });

        // === Daftar tes aktif + statistik per user ===
        $tests = PsyTest::where('is_active', true)
            ->withCount('questions')
            ->orderByDesc('id')
            ->get();

        $stats = [];
        foreach ($tests as $t) {
            $t->title      = $t->name;
            $t->locked     = false;   // atur kalau ada rule membership
            $t->is_premium = false;

            $userAttempts = PsyAttempt::where('user_id', $uid)
                ->where('test_id', $t->id)
                ->whereNotNull('submitted_at')
                ->get();

            $attemptsCnt = $userAttempts->count();
            $scores      = $userAttempts->map(fn($a) => (int) $a->total_score);

            $avg  = $scores->count() ? round($scores->avg(), 2) : 0;
            $best = $scores->count() ? (int) $scores->max() : 0;

            $stats[$t->id] = (object) [
                'attempts'  => $attemptsCnt,
                'avg_score' => $avg,
                'best'      => $best,
            ];
        }

        // === Rekomendasi profil dari attempt terakhir ===
        $last = PsyAttempt::where('user_id', $uid)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        $recommendation = null;
        if ($last) {
            $total = (int) $last->total_score;

            // Cari profil berdasar rentang min_total..max_total (tanpa user_id)
            $prof = PsyProfile::query()
                ->where('test_id', (string) $last->test_id)
                ->where('min_total', '<=', $total)
                ->where(function ($q) use ($total) {
                    $q->whereNull('max_total')
                      ->orWhere('max_total', '>=', $total);
                })
                ->orderByDesc('min_total')
                ->first();

            if ($prof) {
                $recommendation = [
                    'title' => $prof->name,
                    'desc'  => $prof->description ?? '',
                ];
            }
        }

        // === Nama route untuk Blade ===
        $routeNames = [
            'take_show'    => 'app.psytests.show',
            'attempt_show' => null, // tidak ada halaman detail attempt user → fallback "—"
        ];

        return view('app.psychology.dashboard', compact(
            'recommendation',
            'tests',
            'stats',
            'routeNames',
            'attempts'
        ));
    }
}
