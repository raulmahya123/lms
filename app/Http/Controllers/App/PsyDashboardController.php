<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\PsyAttempt;
use App\Models\PsyTest;
use Illuminate\Http\Request;
use App\Services\PsyAccess;
use Illuminate\Support\Facades\Route;

class PsyDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // daftar tes + flag locked + hitung stats per user
        $tests = PsyTest::withCount('questions')->get()->map(function ($t) use ($user) {
            $t->locked = !PsyAccess::canAccess($user, $t);
            return $t;
        });

        $stats = PsyAttempt::selectRaw('test_id, COUNT(*) attempts, MAX(total_score) best, AVG(total_score) avg_score')
            ->where('user_id', $user->id)
            ->groupBy('test_id')
            ->get()
            ->keyBy('test_id');

        // histori (10 terbaru)
        $attempts = PsyAttempt::with('test')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // rekomendasi based on latest
        $latest = PsyAttempt::where('user_id', $user->id)->latest()->first();
        $recommendation = null;
        if ($latest) {
            $profile = PsyAccess::findProfile($latest->test_id, (int) $latest->total_score);
            if ($profile) {
                $recommendation = ['title' => $profile->name, 'desc' => $profile->description];
            }
        }

        // Tentukan nama route yang sudah ada untuk MULAI TES & DETAIL ATTEMPT
        $routeNames = [
            'take_show' => Route::has('app.psy-tests.show') ? 'app.psy-tests.show'
                          : (Route::has('psy-tests.show') ? 'psy-tests.show' : null),
            'attempt_show' => Route::has('app.psy-attempts.show') ? 'app.psy-attempts.show'
                             : (Route::has('psy-attempts.show') ? 'psy-attempts.show' : null),
        ];

        return view('app/psychology/dashboard', compact('tests', 'stats', 'attempts', 'recommendation', 'routeNames'));
    }
}
