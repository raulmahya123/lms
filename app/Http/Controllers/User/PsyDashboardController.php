<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PsyAttempt;
use App\Models\PsyTest;
use App\Models\PsyProfile;
use App\Services\Psychology\PsyTestFlowService;
use Illuminate\Support\Facades\Auth;

class PsyDashboardController extends Controller
{
    public function __construct(private PsyTestFlowService $psyFlow)
    {
    }

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
            ->select(['id', 'test_id', 'user_id', 'submitted_at', 'created_at', 'total_score', 'result_key'])
            ->latest('submitted_at')
            ->paginate(10);

        $profilesByTest = PsyProfile::query()
            ->whereIn('test_id', $attempts->getCollection()->pluck('test_id')->filter()->unique()->values())
            ->orderByDesc('min_total')
            ->get(['id', 'test_id', 'key', 'name', 'min_total', 'max_total'])
            ->groupBy('test_id');

        // Normalisasi judul untuk Blade (title dari name)
        $attempts->getCollection()->transform(function ($a) use ($profilesByTest) {
            if ($a->relationLoaded('test') && $a->test) {
                $a->test->title = $a->test->name;
            }
            $profile = $profilesByTest
                ->get($a->test_id, collect())
                ->first(function ($profile) use ($a) {
                    $score = (int) $a->total_score;

                    return (int) $profile->min_total <= $score
                        && (is_null($profile->max_total) || (int) $profile->max_total >= $score);
                });

            $a->profile_name = $profile?->name ?: ($a->result_key ?: '-');

            return $a;
        });

        // === Daftar tes aktif + statistik per user ===
        $tests = PsyTest::where('is_active', true)
            ->select(['id', 'name', 'slug', 'is_active', 'created_at'])
            ->withCount('questions')
            ->orderByDesc('id')
            ->get();

        $statsRaw = PsyAttempt::query()
            ->where('user_id', $uid)
            ->whereNotNull('submitted_at')
            ->select('test_id')
            ->selectRaw('COUNT(*) as attempts')
            ->selectRaw('AVG(total_score) as avg_score')
            ->selectRaw('MAX(total_score) as best')
            ->groupBy('test_id')
            ->get()
            ->keyBy('test_id');

        $stats = [];
        foreach ($tests as $t) {
            $t->title      = $t->name;
            $t->locked     = false;   // atur kalau ada rule membership
            $t->is_premium = false;

            $row = $statsRaw->get($t->id);

            $stats[$t->id] = (object) [
                'attempts'  => (int) ($row->attempts ?? 0),
                'avg_score' => round((float) ($row->avg_score ?? 0), 2),
                'best'      => (int) ($row->best ?? 0),
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
            $prof = $this->psyFlow->profileForScore((string) $last->test_id, $total);

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
