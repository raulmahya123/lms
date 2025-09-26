<?php

namespace App\Http\Controllers;

use App\Models\PsyProfile;
use App\Models\PsyTest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PsyProfileController extends Controller
{
    /** List + filter (by test & keyword) */
    public function index(Request $r)
    {
        $q  = $r->string('q')->toString();
        $testId = $r->string('test_id')->toString();

        $profiles = PsyProfile::query()
            ->with(['test:id,name,track'])
            ->when($testId, fn($qq) => $qq->where('test_id', $testId))
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('key', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $tests = PsyTest::orderBy('name')->get(['id', 'name', 'track']);

        return view('admin.psy_profiles.index', compact('profiles', 'tests', 'q', 'testId'));
    }

    /** Show form create */
    public function create()
    {
        $tests = PsyTest::orderBy('name')->get(['id', 'name', 'track']);
        return view('admin.psy_profiles.create', compact('tests'));
    }

    /** Store */
    public function store(Request $r)
    {
        $data = $r->validate([
            'test_id'     => ['required', 'uuid', 'exists:psy_tests,id'],
            'key'         => ['required', 'string', 'max:100', Rule::unique('psy_profiles')
                ->where(fn($q) => $q->where('test_id', $r->input('test_id')))],
            'name'        => ['required', 'string', 'max:150'],
            'min_total'   => ['required', 'integer', 'min:0'],
            'max_total'   => ['required', 'integer', 'gte:min_total'],
            'description' => ['nullable', 'string'],
        ]);

        // ← penting: isi user_id dari user yang login
        $data['user_id'] = auth()->id();

        PsyProfile::create($data);

        return redirect()
            ->route('admin.psy-profiles.index', ['test_id' => $data['test_id']])
            ->with('ok', 'Profile berhasil dibuat.');
    }
    /** Edit */
    public function edit(PsyProfile $psy_profile)
    {
        $tests = PsyTest::orderBy('name')->get(['id', 'name', 'track']);
        return view('admin.psy_profiles.edit', ['profile' => $psy_profile, 'tests' => $tests]);
    }

    /** Update */
    public function update(Request $r, PsyProfile $psy_profile)
    {
        $data = $r->validate([
            'test_id'     => ['required', 'uuid', 'exists:psy_tests,id'],
            'key'         => [
                'required',
                'string',
                'max:100',
                Rule::unique('psy_profiles')->ignore($psy_profile->id)->where(fn($q) => $q->where('test_id', $r->input('test_id')))
            ],
            'name'        => ['required', 'string', 'max:150'],
            'min_total'   => ['required', 'integer', 'min:0'],
            'max_total'   => ['required', 'integer', 'gte:min_total'],
            'description' => ['nullable', 'string'],
        ]);

        $psy_profile->update($data);

        return redirect()
            ->route('admin.psy-profiles.index', ['test_id' => $data['test_id']])
            ->with('ok', 'Profile diperbarui.');
    }

    /** Delete */
    public function destroy(PsyProfile $psy_profile)
    {
        $psy_profile->delete();
        return back()->with('ok', 'Profile dihapus.');
    }
}
