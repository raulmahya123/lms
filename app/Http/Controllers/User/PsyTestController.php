<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PsyTest;
use Illuminate\Http\Request;

class PsyTestController extends Controller
{
    /** daftar nilai yang diizinkan (sinkron dgn Admin) */
    private const TRACKS = ['backend','frontend','fullstack','qa','devops','pm','custom'];
    private const TYPES  = ['likert','mcq','iq','disc','big5','custom'];

    /**
     * GET /psy-tests
     * List semua tes aktif untuk user, dengan filter & pencarian.
     */
    public function index(Request $r)
    {
        $tests = PsyTest::query()
            ->where('is_active', true)
            ->when($r->filled('q'), function ($q) use ($r) {
                $term = trim($r->q);
                $q->where(function ($qq) use ($term) {
                    $qq->where('name','like',"%{$term}%")
                       ->orWhere('slug','like',"%{$term}%");
                });
            })
            ->when($r->filled('track') && in_array($r->track, self::TRACKS, true),
                fn($q) => $q->where('track', $r->track))
            ->when($r->filled('type') && in_array($r->type, self::TYPES, true),
                fn($q) => $q->where('type', $r->type))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('app.psy_tests.index', [
            'tests'  => $tests,
            'tracks' => self::TRACKS,
            'types'  => self::TYPES,
            'q'      => $r->q,
            'track'  => $r->track,
            'type'   => $r->type,
        ]);
    }

    /**
     * GET /psy-tests/{slugOrId}
     * Tampilkan detail test (aktif) beserta daftar pertanyaan & options.
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

        return view('app.psy_tests.show', compact('test'));
    }
}
