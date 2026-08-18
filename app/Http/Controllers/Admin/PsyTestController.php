<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PsyTest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PsyTestController extends Controller
{
    /** daftar nilai yang diizinkan */
    private const TRACKS = ['backend', 'frontend', 'fullstack', 'qa', 'devops', 'pm', 'custom'];
    private const TYPES  = ['likert', 'mcq', 'iq', 'disc', 'big5', 'custom'];

    public function index(Request $r)
    {
        $filters = $r->validate([
            'q'     => ['nullable', 'string', 'max:100'],
            'track' => ['nullable', Rule::in(self::TRACKS)],
            'type'  => ['nullable', Rule::in(self::TYPES)],
        ]);
        $term = trim((string) ($filters['q'] ?? ''));

        $tests = PsyTest::query()
            ->select(['id', 'name', 'slug', 'track', 'type', 'time_limit_min', 'is_active', 'created_at'])
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->when($filters['track'] ?? null, fn($q, $track) => $q->where('track', $track))
            ->when($filters['type'] ?? null,  fn($q, $type) => $q->where('type',  $type))
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
        return view('admin.psy_tests.create', [
            'tracks' => self::TRACKS,
            'types'  => self::TYPES,
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'           => ['required', 'string', 'max:160'],
            'slug'           => ['nullable', 'alpha_dash', 'unique:psy_tests,slug'],
            'track'          => ['required', 'in:' . implode(',', self::TRACKS)],
            'type'           => ['required', 'in:' . implode(',', self::TYPES)],
            'time_limit_min' => ['nullable', 'integer', 'min:1', 'max:600'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        // default slug dari name kalau kosong
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        // normalisasi boolean
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $psy_test = PsyTest::create($data);

        return redirect()
            ->route('admin.psy-tests.show', $psy_test)
            ->with('ok', 'Test created');
    }

    public function show(PsyTest $psy_test)
    {
        $psy_test->loadCount(['questions']);
        return view('admin.psy_tests.show', compact('psy_test'));
    }

    public function edit(PsyTest $psy_test)
    {
        return view('admin.psy_tests.edit', [
            'psy_test' => $psy_test,
            'tracks'   => self::TRACKS,
            'types'    => self::TYPES,
        ]);
    }

    public function update(Request $r, PsyTest $psy_test)
    {
        $data = $r->validate([
            'name'           => ['required', 'string', 'max:160'],
            'slug'           => ['nullable', 'alpha_dash', 'unique:psy_tests,slug,' . $psy_test->id],
            'track'          => ['required', 'in:' . implode(',', self::TRACKS)],
            'type'           => ['required', 'in:' . implode(',', self::TYPES)],
            'time_limit_min' => ['nullable', 'integer', 'min:1', 'max:600'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $psy_test->update($data);

        return redirect()
            ->route('admin.psy-tests.show', $psy_test)
            ->with('ok', 'Test updated');
    }

    public function destroy(PsyTest $psy_test)
    {
        // Opsional: cegah hapus jika sudah ada attempt
        if (method_exists($psy_test, 'attempts')) {
            $psy_test->loadCount('attempts');
            if ($psy_test->attempts_count > 0) {
                return back()->with('err', 'Tidak bisa hapus: sudah ada attempts pada test ini.');
            }
        }

        $psy_test->delete();

        return redirect()
            ->route('admin.psy-tests.index')
            ->with('ok', 'Test deleted.');
    }
}
