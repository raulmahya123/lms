<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PsyTest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PsyTestController extends Controller
{
    /** daftar nilai yang diizinkan */
    private const TRACKS = ['backend','frontend','fullstack','qa','devops','pm','custom'];
    private const TYPES  = ['likert','mcq','iq','disc','big5','custom'];

    public function index(Request $r)
{
    $tests = PsyTest::query()
        ->when($r->filled('q'), function ($q) use ($r) {
            $term = trim($r->q);
            $q->where(function ($qq) use ($term) {
                $qq->where('name', 'like', "%{$term}%")
                   ->orWhere('slug', 'like', "%{$term}%");
            });
        })
        ->when($r->filled('track'), fn($q) => $q->where('track', $r->track))
        ->when($r->filled('type'),  fn($q) => $q->where('type',  $r->type))
        ->latest('id')
        ->paginate(20)
        ->withQueryString();

    return view('admin.psy_tests.index', [
        'tests'  => $tests,
        'tracks' => self::TRACKS,
        'types'  => self::TYPES,
    ]);
}


    public function create()
    {
        // kalau form butuh array tracks/types, bisa kirim dari sini juga
        return view('admin.psy_tests.create', [
            'tracks' => self::TRACKS,
            'types'  => self::TYPES,
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'           => ['required','string','max:160'],
            'slug'           => ['nullable','alpha_dash','unique:psy_tests,slug'],
            'track'          => ['required','in:'.implode(',', self::TRACKS)],
            'type'           => ['required','in:'.implode(',', self::TYPES)],
            'time_limit_min' => ['nullable','integer','min:1','max:600'],
            // jangan "required|boolean" untuk checkbox, karena unchecked = tidak terkirim
            'is_active'      => ['nullable','boolean'],
        ]);

        // default slug dari name kalau kosong
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // normalisasi boolean
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $test = PsyTest::create($data);

        return redirect()
            ->route('admin.psy-tests.show', $test)
            ->with('ok', 'Test created');
    }

    public function show(PsyTest $psy_test)
    {
        // pastikan relasi ada di model sebelum loadCount
        $psy_test->loadCount(['questions'/*,'profiles'*/]);

        return view('admin.psy_tests.show', [
            'test' => $psy_test,
        ]);
    }

    public function edit(PsyTest $psy_test)
    {
        return view('admin.psy_tests.edit', [
            'test'   => $psy_test,
            'tracks' => self::TRACKS,
            'types'  => self::TYPES,
        ]);
    }

    public function update(Request $r, PsyTest $psy_test)
    {
        $data = $r->validate([
            'name'           => ['required','string','max:160'],
            // unique ignore current id
            'slug'           => ['nullable','alpha_dash','unique:psy_tests,slug,'.$psy_test->id],
            'track'          => ['required','in:'.implode(',', self::TRACKS)],
            'type'           => ['required','in:'.implode(',', self::TYPES)],
            'time_limit_min' => ['nullable','integer','min:1','max:600'],
            'is_active'      => ['nullable','boolean'],
        ]);

        // jaga-jaga: jika slug kosong, buat dari name
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $psy_test->update($data);

        return redirect()
            ->route('admin.psy-tests.show', $psy_test)
            ->with('ok', 'Test updated');
    }

    public function destroy(PsyTest $psy_test)
    {
        $psy_test->delete();

        return redirect()
            ->route('admin.psy-tests.index')
            ->with('ok', 'Test deleted');
    }
}
