<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PsyTest;
use App\Models\PsyQuestion;
use App\Models\PsyAttempt;
use App\Models\PsyAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PsyTestController extends Controller
{
    /** daftar nilai yang diizinkan (sinkron dgn Admin) */
    private const TRACKS = ['backend','frontend','fullstack','qa','devops','pm','custom'];
    private const TYPES  = ['likert','mcq','iq','disc','big5','custom'];

    public function __construct()
    {
        // Tes biasanya untuk user login; kalau mau publik, hapus middleware ini.
        $this->middleware('auth');
    }

    /** Normalisasi & whitelist sort */
    private function normalizeSort(Request $r): array
    {
        $allowedSorts = ['latest','name','questions']; // latest=id desc, name asc, questions=questions_count desc
        $sort = in_array($r->get('sort'), $allowedSorts, true) ? $r->get('sort') : 'latest';
        return [$sort];
    }

    /** Ambil perPage aman (10..100) */
    private function perPage(Request $r): int
    {
        $pp = (int) $r->get('per_page', 20);
        return max(10, min(100, $pp));
    }

    /**
     * GET /psy-tests
     * List semua tes aktif untuk user, dengan filter, pencarian, sorting, dan status attempt.
     */
    public function index(Request $r)
    {
        [$sort] = $this->normalizeSort($r);
        $perPage = $this->perPage($r);

        $q = PsyTest::query()
            ->where('is_active', true)
            ->when($r->filled('q'), function ($q) use ($r) {
                $term = trim($r->q);
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', "%{$term}%")
                       ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->when($r->filled('track') && in_array($r->track, self::TRACKS, true),
                fn($q) => $q->where('track', $r->track))
            ->when($r->filled('type') && in_array($r->type, self::TYPES, true),
                fn($q) => $q->where('type', $r->type))
            ->withCount('questions');

        // Sorting
        if ($sort === 'name') {
            $q->orderBy('name')->orderBy('id', 'desc');
        } elseif ($sort === 'questions') {
            $q->orderBy('questions_count', 'desc')->orderBy('id', 'desc');
        } else {
            $q->latest('id');
        }

        $tests = $q->paginate($perPage)->withQueryString();

        // === Status attempt aktif per test (untuk tombol Resume/Start) ===
        $testIds = collect($tests->items())->pluck('id')->all();
        $activeAttempts = PsyAttempt::where('user_id', Auth::id())
            ->whereNull('submitted_at')
            ->whereIn('test_id', $testIds)
            ->get(['id','test_id','started_at']);

        $attemptByTest = $activeAttempts->keyBy('test_id');

        // Hitung jumlah jawaban per attempt untuk indikasi progres
        $answerCountsByAttempt = PsyAnswer::whereIn('attempt_id', $activeAttempts->pluck('id'))
            ->selectRaw('attempt_id, COUNT(*) as c')
            ->groupBy('attempt_id')
            ->pluck('c', 'attempt_id');

        // Flag bisa mulai (punya pertanyaan) dan apakah sedang berjalan
        $canStartByTest = [];
        $isOngoingByTest = [];
        foreach ($tests as $t) {
            $canStartByTest[$t->id] = ($t->questions_count ?? 0) > 0;
            $isOngoingByTest[$t->id] = $attemptByTest->has($t->id);
        }

        return view('app.psy_tests.index', [
            'tests'                 => $tests,
            'tracks'                => self::TRACKS,
            'types'                 => self::TYPES,

            // filter state
            'q'                     => $r->q,
            'track'                 => $r->track,
            'type'                  => $r->type,
            'sort'                  => $sort,
            'perPage'               => $perPage,

            // attempt status
            'attemptByTest'         => $attemptByTest,         // key: test_id → attempt model
            'answerCountsByAttempt' => $answerCountsByAttempt, // key: attempt_id → count answered
            'canStartByTest'        => $canStartByTest,        // key: test_id → bool
            'isOngoingByTest'       => $isOngoingByTest,       // key: test_id → bool
        ]);
    }

    /**
     * GET /psy-tests/{slugOrId}
     * Tampilkan detail test (aktif) beserta daftar pertanyaan & options + CTA Start/Resume.
     */
    public function show(Request $r, string|int $slugOrId)
    {
        $test = PsyTest::query()
            ->where('is_active', true)
            ->where(function ($q) use ($slugOrId) {
                $q->where('id', $slugOrId)->orWhere('slug', $slugOrId);
            })
            ->withCount('questions')
            ->firstOrFail();

        // (opsional) tampilkan daftar soal di halaman detail
        $test->load(['questions' => function ($q) {
            $q->orderBy('ordering')->orderBy('id')
              ->with(['options' => function ($qq) {
                  $qq->orderBy('ordering')->orderBy('id');
              }]);
        }]);

        // Attempt aktif user (kalau ada) untuk tombol Resume + progress
        $attempt = PsyAttempt::where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->whereNull('submitted_at')
            ->first();

        $answeredIds = [];
        $resumeNextId = null;
        $secondsLeft = null;
        $startedAtMs = null;
        $timeLimitMin = (int) ($test->time_limit_min ?? 0);

        if ($attempt) {
            $answeredIds = PsyAnswer::where('attempt_id', $attempt->id)->pluck('question_id')->all();

            // Next yang belum dijawab
            $orderedQids = $test->questions->pluck('id')->all();
            $resumeNextId = collect($orderedQids)->first(fn($id) => !in_array($id, $answeredIds, true)) ?? (count($orderedQids) ? $orderedQids[0] : null);

            // Hitung sisa waktu berdasarkan started_at attempt
            if ($timeLimitMin > 0 && $attempt->started_at) {
                $elapsed    = now()->diffInSeconds($attempt->started_at);
                $secondsLeft = max(0, $timeLimitMin * 60 - $elapsed);
                $startedAtMs = $attempt->started_at->valueOf();
            }
        } else {
            // fallback seed waktu untuk UI (jika mau tampilkan timer sebelum Start)
            if ($timeLimitMin > 0) {
                $sessKey = "psy.{$test->getKey()}.started_at";
                if (!Session::has($sessKey)) {
                    Session::put($sessKey, now()->toIso8601String());
                }
                $startedAtIso = Session::get($sessKey);
                $startedAtMs  = Carbon::parse($startedAtIso)->valueOf();
                $secondsLeft  = $timeLimitMin * 60; // full duration (belum mulai attempt)
            }
        }

        // Pertanyaan pertama (untuk CTA "Mulai Sekarang")
        $firstQuestionId = optional($test->questions->first())->id;

        // Progress kasar (answered / total)
        $answeredCount = count($answeredIds);
        $total         = (int) ($test->questions_count ?? 0);
        $pct           = $total > 0 ? (int) floor(($answeredCount / $total) * 100) : 0;

        return view('app.psy_tests.show', [
            'test'           => $test,
            'firstQuestionId'=> $firstQuestionId,

            // attempt status
            'attempt'        => $attempt,
            'answeredIds'    => $answeredIds,
            'answeredCount'  => $answeredCount,
            'total'          => $total,
            'pct'            => $pct,
            'resumeNextId'   => $resumeNextId,

            // waktu (untuk countdown di UI)
            'timeLimitMin'   => $timeLimitMin,
            'secondsLeft'    => $secondsLeft,
            'startedAtMs'    => $startedAtMs,
        ]);
    }
}
